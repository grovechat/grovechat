<?php

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('super admin can view workspace trash list', function () {
    $admin = User::factory()->create([
        'is_super_admin' => true,
    ]);

    $workspace = Workspace::factory()->create([
        'owner_id' => User::factory()->create()->id,
    ]);
    $workspace->delete();

    actingAs($admin, 'admin')
        ->get(route('admin.get-workspace-trash'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/workspace/Trash')
            ->has('workspace_trash_list', 1)
            ->where('workspace_trash_list.0.id', (string) $workspace->id)
            ->where('workspace_trash_list.0.name', $workspace->name)
            ->etc()
        );
});

test('super admin can restore a trashed workspace', function () {
    $admin = User::factory()->create([
        'is_super_admin' => true,
    ]);

    $workspace = Workspace::factory()->create([
        'owner_id' => User::factory()->create()->id,
    ]);
    $workspace->delete();

    actingAs($admin, 'admin')
        ->put(route('admin.restore-workspace', ['id' => $workspace->id]))
        ->assertRedirect();

    expect(Workspace::onlyTrashed()->whereKey($workspace->id)->exists())->toBeFalse();
    expect(Workspace::query()->whereKey($workspace->id)->exists())->toBeTrue();
});

test('non-super-admin cannot view or restore workspace trash', function () {
    $user = User::factory()->create([
        'is_super_admin' => false,
    ]);
    $workspace = Workspace::factory()->create([
        'owner_id' => User::factory()->create()->id,
    ]);
    $workspace->delete();

    actingAs($user, 'admin')
        ->get(route('admin.get-workspace-trash'))
        ->assertForbidden();

    actingAs($user, 'admin')
        ->put(route('admin.restore-workspace', ['id' => $workspace->id]))
        ->assertForbidden();
});
