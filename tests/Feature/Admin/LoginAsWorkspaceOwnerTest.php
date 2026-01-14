<?php

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('super admin can open workspace as owner in web guard without losing admin guard', function () {
    $admin = User::factory()->create([
        'is_super_admin' => true,
    ]);

    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $owner->workspaces()->attach($workspace->id, ['role' => WorkspaceRole::OWNER->value]);

    $response = actingAs($admin, 'admin')
        ->get(route('login-as-workspace-owner', ['id' => $workspace->id]));

    $response->assertRedirect(route('workspace.dashboard', ['slug' => $workspace->slug], absolute: false));
    expect(auth('admin')->id())->toBe($admin->id);
    expect(auth('web')->id())->toBe($owner->id);
});

test('logging out web guard does not affect admin guard', function () {
    $admin = User::factory()->create([
        'is_super_admin' => true,
    ]);

    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create([
        'owner_id' => $owner->id,
    ]);
    $owner->workspaces()->attach($workspace->id, ['role' => WorkspaceRole::OWNER->value]);

    actingAs($admin, 'admin')
        ->get(route('login-as-workspace-owner', ['id' => $workspace->id]));

    expect(auth('admin')->id())->toBe($admin->id);
    expect(auth('web')->id())->toBe($owner->id);

    post(route('logout.web'))
        ->assertRedirect(route('home', absolute: false));

    expect(auth('admin')->id())->toBe($admin->id);
    expect(auth('web')->check())->toBeFalse();
});
