<?php

use App\Enums\UserOnlineStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\WithWorkspace;

uses(RefreshDatabase::class, WithWorkspace::class);

beforeEach(function () {
    $this->user = $this->createUserWithWorkspace([], [
        'name' => 'Test Workspace',
    ]);
});

test('authenticated user can view user list page', function () {
    $member = User::factory()->create([
        'name' => '客服A',
        'email' => 'a@example.com',
    ]);
    $this->workspace->users()->attach($member->id, [
        'role' => 'operator',
        'online_status' => UserOnlineStatus::OFFLINE->value,
    ]);

    $this->actingAs($this->user)
        ->get(route('show-teammate-list', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('teammate/List')
            ->has('user_list')
            ->has('user_list.0', fn (Assert $item) => $item
                ->hasAll(['user_id', 'user_name', 'user_avatar', 'user_email', 'role', 'user_online_status', 'show_remove_button'])
                ->etc()
            )
        );
});

test('operator cannot access manage center pages', function () {
    $workspace = $this->workspace;

    $operator = User::factory()->create([
        'name' => '普通客服',
        'email' => 'operator@example.com',
    ]);
    $workspace->users()->attach($operator->id, ['role' => 'operator']);

    $this->actingAs($operator)
        ->get(route('get-current-workspace', ['slug' => $this->workspaceSlug()]))
        ->assertForbidden();

    $this->actingAs($operator)
        ->get(route('show-teammate-list', ['slug' => $this->workspaceSlug()]))
        ->assertForbidden();
});

test('workspace member can update own online status from sidebar', function () {
    $operator = User::factory()->create([
        'name' => '普通客服',
        'email' => 'operator-online@example.com',
    ]);
    $this->workspace->users()->attach($operator->id, [
        'role' => 'operator',
        'online_status' => UserOnlineStatus::OFFLINE->value,
    ]);

    $this->actingAs($operator)
        ->put(route('update-my-online-status', ['slug' => $this->workspaceSlug()]), [
            'online_status' => UserOnlineStatus::ONLINE->value,
        ])
        ->assertRedirect();

    expect($this->workspace->users()->whereKey($operator->id)->firstOrFail()->pivot->online_status)
        ->toBe(UserOnlineStatus::ONLINE->value);
});

test('authenticated user can view create teammate page with options', function () {
    User::factory()->create([
        'name' => '待添加用户',
        'email' => 'available@example.com',
    ]);

    $this->actingAs($this->user)
        ->get(route('show-create-teammate-page', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('teammate/Create')
            ->has('role_options', 2)
            ->has('available_users')
            ->where('role_options.0.value', 'admin')
            ->where('role_options.1.value', 'operator')
            ->etc()
        );
});

test('authenticated user can view edit user page with role options', function () {
    $member = User::factory()->create([
        'name' => '客服Z',
        'email' => 'z@example.com',
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->get(route('show-edit-teammate-page', ['slug' => $this->workspaceSlug(), 'id' => $member->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('teammate/Edit')
            ->has('user_form')
            ->has('role_options', 2)
            ->where('role_options.0.value', 'admin')
            ->where('role_options.1.value', 'operator')
            ->etc()
        );
});

test('can add an existing user into current workspace', function () {
    $candidate = User::factory()->create([
        'name' => '客服B',
        'email' => 'b@example.com',
    ]);

    $this->actingAs($this->user)
        ->post(route('create-teammate', ['slug' => $this->workspaceSlug()]), [
            'user_id' => $candidate->id,
            'nickname' => '小B',
            'role' => 'operator',
        ])
        ->assertRedirect(route('show-teammate-list', ['slug' => $this->workspaceSlug()]));

    expect($this->workspace->users()->whereKey($candidate->id)->exists())->toBeTrue();
    expect($this->workspace->users()->whereKey($candidate->id)->firstOrFail()->pivot->role)->toBe('operator');
    expect($this->workspace->users()->whereKey($candidate->id)->firstOrFail()->pivot->nickname)->toBe('小B');
});

test('cannot add a user with owner role', function () {
    $candidate = User::factory()->create([
        'name' => '非法 Owner',
        'email' => 'owner-like@example.com',
    ]);

    $this->actingAs($this->user)
        ->post(route('create-teammate', ['slug' => $this->workspaceSlug()]), [
            'user_id' => $candidate->id,
            'role' => 'owner',
        ])
        ->assertSessionHasErrors(['role']);
});

test('cannot add workspace owner as teammate', function () {
    $member = User::factory()->create([
        'name' => 'Owner-外部同名无关',
        'email' => 'other@example.com',
    ]);

    $this->actingAs($this->user)
        ->post(route('create-teammate', ['slug' => $this->workspaceSlug()]), [
            'user_id' => $this->user->id,
            'role' => 'operator',
        ])
        ->assertSessionHasErrors(['user_id']);
});

test('cannot add duplicate workspace member', function () {
    $candidate = User::factory()->create([
        'name' => '客服重复',
        'email' => 'dup@example.com',
    ]);
    $this->workspace->users()->attach($candidate->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->post(route('create-teammate', ['slug' => $this->workspaceSlug()]), [
            'user_id' => $candidate->id,
            'role' => 'operator',
        ])
        ->assertSessionHasErrors(['user_id']);
});

test('owner cannot change own role', function () {
    $this->actingAs($this->user)
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $this->user->id]), [
            'role' => 'admin',
        ])
        ->assertForbidden();

    $role = $this->workspace->users()->whereKey($this->user->id)->firstOrFail()->pivot->role;
    expect($role)->toBe('owner');
});

test('owner can update another user role', function () {
    $member = User::factory()->create([
        'name' => '客服D',
        'email' => 'd@example.com',
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'nickname' => '对外昵称D',
            'role' => 'admin',
        ])
        ->assertRedirect(route('show-teammate-list', ['slug' => $this->workspaceSlug()]));

    expect($this->workspace->users()->whereKey($member->id)->firstOrFail()->pivot->role)->toBe('admin');
    expect($this->workspace->users()->whereKey($member->id)->firstOrFail()->pivot->nickname)->toBe('对外昵称D');
});

test('owner cannot set another user role to owner', function () {
    $member = User::factory()->create([
        'name' => '客服-不可升 Owner',
        'email' => 'no-owner@example.com',
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'role' => 'owner',
        ])
        ->assertForbidden();

    $role = $this->workspace->users()->whereKey($member->id)->firstOrFail()->pivot->role;
    expect($role)->toBe('operator');
});

test('admin cannot change operator role', function () {
    $admin = User::factory()->create([
        'name' => '管理员X',
        'email' => 'admin-x@example.com',
    ]);
    $this->workspace->users()->attach($admin->id, ['role' => 'admin']);

    $member = User::factory()->create([
        'name' => '客服-角色不可被管理员改',
        'email' => 'no-admin-role-change@example.com',
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($admin)
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'role' => 'admin',
        ])
        ->assertForbidden();

    $role = $this->workspace->users()->whereKey($member->id)->firstOrFail()->pivot->role;
    expect($role)->toBe('operator');
});

test('can update user online status from list', function () {
    $member = User::factory()->create([
        'name' => '客服E',
        'email' => 'e@example.com',
    ]);
    $this->workspace->users()->attach($member->id, [
        'role' => 'operator',
        'online_status' => UserOnlineStatus::OFFLINE->value,
    ]);

    $this->actingAs($this->user)
        ->put(route('update-teammate-online-status', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'online_status' => UserOnlineStatus::ONLINE->value,
        ])
        ->assertRedirect();

    $member->refresh();
    expect($this->workspace->users()->whereKey($member->id)->firstOrFail()->pivot->online_status)
        ->toBe(UserOnlineStatus::ONLINE->value);
});

test('cannot delete current logged in user', function () {
    $this->actingAs($this->user)
        ->delete(route('remove-teammate', ['slug' => $this->workspaceSlug(), 'id' => $this->user->id]))
        ->assertForbidden();
});

test('delete teammate only detaches from workspace', function () {
    $member = User::factory()->create([
        'name' => '待移除客服',
        'email' => 'remove@example.com',
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->delete(route('remove-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]))
        ->assertRedirect();

    expect(User::query()->whereKey($member->id)->exists())->toBeTrue();
    expect($this->workspace->users()->whereKey($member->id)->exists())->toBeFalse();
});

test('admin can detach operator from workspace', function () {
    $admin = User::factory()->create([
        'name' => '管理员Y',
        'email' => 'admin-y@example.com',
    ]);
    $this->workspace->users()->attach($admin->id, ['role' => 'admin']);

    $operator = User::factory()->create([
        'name' => '客服-可被管理员移除',
        'email' => 'operator-remove@example.com',
    ]);
    $this->workspace->users()->attach($operator->id, ['role' => 'operator']);

    $this->actingAs($admin)
        ->delete(route('remove-teammate', ['slug' => $this->workspaceSlug(), 'id' => $operator->id]))
        ->assertRedirect();

    expect($this->workspace->users()->whereKey($operator->id)->exists())->toBeFalse();
});

test('admin cannot detach another admin from workspace', function () {
    $admin = User::factory()->create([
        'name' => '管理员1',
        'email' => 'admin1@example.com',
    ]);
    $this->workspace->users()->attach($admin->id, ['role' => 'admin']);

    $admin2 = User::factory()->create([
        'name' => '管理员2',
        'email' => 'admin2@example.com',
    ]);
    $this->workspace->users()->attach($admin2->id, ['role' => 'admin']);

    $this->actingAs($admin)
        ->delete(route('remove-teammate', ['slug' => $this->workspaceSlug(), 'id' => $admin2->id]))
        ->assertForbidden();

    expect($this->workspace->users()->whereKey($admin2->id)->exists())->toBeTrue();
});
