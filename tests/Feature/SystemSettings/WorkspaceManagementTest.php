<?php

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\WithWorkspace;

uses(RefreshDatabase::class, WithWorkspace::class);

beforeEach(function () {
    $this->user = $this->createUserWithWorkspace([], [
        'name' => 'Test Workspace',
    ]);
});

test('authenticated user can view workspace management list page', function () {
    // Create a few workspaces with owners
    $ownerA = User::factory()->create();
    $ownerB = User::factory()->create();

    Workspace::factory()->create([
        'name' => 'Acme',
        'owner_id' => $ownerA->id,
    ]);

    Workspace::factory()->create([
        'name' => 'Beta',
        'owner_id' => $ownerB->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('get-workspace-list', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('workspace/List')
            ->has('workspace_list')
            ->has('workspace_list.0', fn (Assert $item) => $item
                ->hasAll(['id', 'name', 'slug', 'created_at', 'members_count', 'owner'])
                ->etc()
            )
        );
});

test('workspace detail shows members with pagination', function () {
    $workspace = Workspace::factory()->create([
        'name' => 'Paged Workspace',
        'owner_id' => User::factory()->create()->id,
    ]);

    // Attach the current user to the workspace so IdentifyWorkspace middleware can resolve currentWorkspace.
    $this->user->workspaces()->attach($workspace->id, ['role' => 'admin']);
    $this->workspace = $workspace;

    $users = User::factory()->count(12)->create();
    foreach ($users as $u) {
        $workspace->users()->attach($u->id, ['role' => 'customer_service']);
    }

    $this->actingAs($this->user)
        ->get(route('show-workspace-detail', ['slug' => $this->workspaceSlug(), 'id' => $workspace->id, 'page' => 1, 'per_page' => 10]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('workspace/Show')
            ->where('workspace_detail.id', (string) $workspace->id)
            ->has('workspace_members', 10)
            ->where('workspace_members_pagination.current_page', 1)
            ->where('workspace_members_pagination.per_page', 10)
            // 当前登录用户也已 attach 到该工作区，因此 total = 12 + 1
            ->where('workspace_members_pagination.total', 13)
        );
});

test('authenticated user can soft delete a workspace they do not own', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $this->actingAs($this->user)
        ->delete(route('delete-workspace', ['slug' => $this->workspaceSlug(), 'id' => $workspace->id]))
        ->assertRedirect();

    $this->assertSoftDeleted('workspaces', ['id' => $workspace->id]);
});

test('user cannot delete a workspace they own', function () {
    $workspace = Workspace::factory()->create([
        'owner_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)
        ->delete(route('delete-workspace', ['slug' => $this->workspaceSlug(), 'id' => $workspace->id]))
        ->assertForbidden();

    expect(Workspace::withTrashed()->whereKey($workspace->id)->exists())->toBeTrue();
    expect(Workspace::withTrashed()->find($workspace->id)?->trashed())->toBeFalse();
});

