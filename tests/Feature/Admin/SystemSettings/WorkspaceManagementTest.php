<?php

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'is_super_admin' => true,
    ]);
});

test('super admin can view workspace management list page', function () {
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

    $this->actingAs($this->user, 'admin')
        ->get(route('admin.get-workspace-list'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/workspace/List')
            ->has('workspace_list')
            ->has('workspace_list_pagination')
            ->where('workspace_list_pagination.current_page', 1)
            ->has('workspace_list.0', fn (Assert $item) => $item
                ->hasAll(['id', 'name', 'slug', 'created_at', 'members_count', 'owner'])
                ->etc()
            )
        );
});

test('workspace management list supports pagination params', function () {
    $owner = User::factory()->create();

    Workspace::factory()->count(12)->create([
        'owner_id' => $owner->id,
    ]);

    $this->actingAs($this->user, 'admin')
        ->get(route('admin.get-workspace-list', ['page' => 2, 'per_page' => 10]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/workspace/List')
            ->has('workspace_list', 2)
            ->where('workspace_list_pagination.current_page', 2)
            ->where('workspace_list_pagination.per_page', 10)
            ->where('workspace_list_pagination.total', 12)
        );
});

test('workspace detail shows members with pagination', function () {
    $workspace = Workspace::factory()->create([
        'name' => 'Paged Workspace',
        'owner_id' => User::factory()->create()->id,
    ]);

    $users = User::factory()->count(12)->create();
    foreach ($users as $u) {
        $workspace->users()->attach($u->id, ['role' => 'operator']);
    }

    $this->actingAs($this->user, 'admin')
        ->get(route('admin.show-workspace-detail', ['id' => $workspace->id, 'page' => 1, 'per_page' => 10]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/workspace/Show')
            ->where('workspace.id', (string) $workspace->id)
            ->where('workspace.members_count', 12)
            ->has('members.items', 10)
            ->where('members.pagination.current_page', 1)
            ->where('members.pagination.per_page', 10)
            ->where('members.pagination.total', 12)
        );
});

test('authenticated user can soft delete a workspace they do not own', function () {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create([
        'owner_id' => $owner->id,
    ]);

    $this->actingAs($this->user, 'admin')
        ->delete(route('admin.delete-workspace', ['id' => $workspace->id]))
        ->assertRedirect();

    $this->assertSoftDeleted('workspaces', ['id' => $workspace->id]);
});

test('user cannot delete a workspace they own', function () {
    $workspace = Workspace::factory()->create([
        'owner_id' => $this->user->id,
    ]);

    $this->actingAs($this->user, 'admin')
        ->delete(route('admin.delete-workspace', ['id' => $workspace->id]))
        ->assertForbidden();

    expect(Workspace::withTrashed()->whereKey($workspace->id)->exists())->toBeTrue();
    expect(Workspace::withTrashed()->find($workspace->id)?->trashed())->toBeFalse();
});
