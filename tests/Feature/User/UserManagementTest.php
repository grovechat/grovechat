<?php

use App\Enums\UserOnlineStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
        'online_status' => UserOnlineStatus::OFFLINE->value,
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->get(route('show-user-list', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('user/List')
            ->has('user_list')
            ->has('user_list.0', fn (Assert $item) => $item
                ->hasAll(['id', 'name', 'avatar', 'email', 'role', 'role_label', 'online_status', 'online_status_label'])
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
        ->get(route('show-user-list', ['slug' => $this->workspaceSlug()]))
        ->assertForbidden();
});

test('authenticated user can view create user page with role options', function () {
    $this->actingAs($this->user)
        ->get(route('show-create-user-page', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('user/Create')
            ->has('user_form')
            ->has('role_options', 2)
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
        ->get(route('show-edit-user-page', ['slug' => $this->workspaceSlug(), 'id' => $member->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('user/Edit')
            ->has('user_form')
            ->has('role_options', 2)
            ->where('role_options.0.value', 'admin')
            ->where('role_options.1.value', 'operator')
            ->etc()
        );
});

test('can create a user in current workspace', function () {
    $this->actingAs($this->user)
        ->post(route('create-user', ['slug' => $this->workspaceSlug()]), [
            'name' => '客服B',
            'nickname' => '小B',
            'avatar' => 'https://example.com/a.png',
            'role' => 'operator',
            'email' => 'b@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])
        ->assertRedirect(route('show-user-list', ['slug' => $this->workspaceSlug()]));

    $created = User::query()->where('email', 'b@example.com')->firstOrFail();
    expect(Hash::check('secret1234', $created->password))->toBeTrue();
    expect($this->workspace->users()->whereKey($created->id)->exists())->toBeTrue();
});

test('cannot create a user with owner role', function () {
    $this->actingAs($this->user)
        ->post(route('create-user', ['slug' => $this->workspaceSlug()]), [
            'name' => '非法 Owner',
            'nickname' => null,
            'avatar' => null,
            'role' => 'owner',
            'email' => 'owner-like@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])
        ->assertSessionHasErrors(['role']);
});

test('can update a user without changing password', function () {
    $member = User::factory()->create([
        'name' => '客服C',
        'email' => 'c@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $oldHash = $member->password;

    $this->actingAs($this->user)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'name' => '客服C-新',
            'nickname' => '小C',
            'avatar' => null,
            'role' => 'admin',
            'email' => 'c@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('show-user-list', ['slug' => $this->workspaceSlug()]));

    $member->refresh();
    expect($member->name)->toBe('客服C-新');
    expect($member->password)->toBe($oldHash);
});

test('admin cannot update own email', function () {
    $admin = User::factory()->create([
        'name' => '管理员A',
        'email' => 'admin-a@example.com',
    ]);
    $this->workspace->users()->attach($admin->id, ['role' => 'admin']);

    $this->actingAs($admin)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $admin->id]), [
            'name' => $admin->name,
            'nickname' => null,
            'avatar' => null,
            'role' => 'admin',
            'email' => 'admin-a-new@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertForbidden();

    $admin->refresh();
    expect($admin->email)->toBe('admin-a@example.com');
});

test('operator cannot update own email', function () {
    $operator = User::factory()->create([
        'name' => '客服O',
        'email' => 'operator-o@example.com',
    ]);
    $this->workspace->users()->attach($operator->id, ['role' => 'operator']);

    $this->actingAs($operator)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $operator->id]), [
            'name' => $operator->name,
            'nickname' => null,
            'avatar' => null,
            'role' => 'operator',
            'email' => 'operator-o-new@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertForbidden();

    $operator->refresh();
    expect($operator->email)->toBe('operator-o@example.com');
});

test('owner can update another user email', function () {
    $member = User::factory()->create([
        'name' => '客服E1',
        'email' => 'e1@example.com',
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'name' => $member->name,
            'nickname' => null,
            'avatar' => null,
            'role' => 'operator',
            'email' => 'e1-new@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('show-user-list', ['slug' => $this->workspaceSlug()]));

    $member->refresh();
    expect($member->email)->toBe('e1-new@example.com');
});

test('admin can update other operator profile fields but cannot update email', function () {
    $admin = User::factory()->create([
        'name' => '管理员B',
        'email' => 'admin-b@example.com',
    ]);
    $this->workspace->users()->attach($admin->id, ['role' => 'admin']);

    $operator = User::factory()->create([
        'name' => '客服P',
        'email' => 'p@example.com',
    ]);
    $this->workspace->users()->attach($operator->id, ['role' => 'operator']);

    $this->actingAs($admin)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $operator->id]), [
            'name' => '客服P-新',
            'nickname' => '新昵称',
            'avatar' => 'https://example.com/a.png',
            'role' => 'operator',
            'email' => $operator->email,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('show-user-list', ['slug' => $this->workspaceSlug()]));

    $operator->refresh();
    expect($operator->name)->toBe('客服P-新');
    expect($operator->nickname)->toBe('新昵称');
    expect($operator->avatar)->toBe('https://example.com/a.png');

    $this->actingAs($admin)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $operator->id]), [
            'name' => $operator->name,
            'nickname' => $operator->nickname,
            'avatar' => $operator->avatar,
            'role' => 'operator',
            'email' => 'p-new@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertForbidden();
});

test('owner can update other operator profile fields', function () {
    $operator = User::factory()->create([
        'name' => '客服X',
        'email' => 'x@example.com',
    ]);
    $this->workspace->users()->attach($operator->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $operator->id]), [
            'name' => '客服X-新',
            'nickname' => '新昵称',
            'avatar' => 'https://example.com/c.png',
            'role' => 'operator',
            'email' => $operator->email,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('show-user-list', ['slug' => $this->workspaceSlug()]));

    $operator->refresh();
    expect($operator->name)->toBe('客服X-新');
});

test('admin cannot update another admin email', function () {
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
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $admin2->id]), [
            'name' => $admin2->name,
            'nickname' => null,
            'avatar' => null,
            'role' => 'admin',
            'email' => 'admin2-new@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertForbidden();

    $admin2->refresh();
    expect($admin2->email)->toBe('admin2@example.com');
});

test('owner cannot update own email', function () {
    $this->actingAs($this->user)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $this->user->id]), [
            'name' => $this->user->name,
            'nickname' => null,
            'avatar' => null,
            'role' => 'owner',
            'email' => 'owner-new@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertForbidden();

    $this->user->refresh();
    expect($this->user->email)->not->toBe('owner-new@example.com');
});

test('operator cannot update any users via manage center', function () {
    $operator = User::factory()->create([
        'name' => '客服Self',
        'email' => 'self@example.com',
    ]);
    $this->workspace->users()->attach($operator->id, ['role' => 'operator']);

    $other = User::factory()->create([
        'name' => '客服Other',
        'email' => 'other@example.com',
    ]);
    $this->workspace->users()->attach($other->id, ['role' => 'operator']);

    $this->actingAs($operator)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $operator->id]), [
            'name' => '客服Self-新',
            'nickname' => '昵称',
            'avatar' => 'https://example.com/b.png',
            'role' => 'operator',
            'email' => $operator->email,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertForbidden();

    $this->actingAs($operator)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $other->id]), [
            'name' => '客服Other-新',
            'nickname' => null,
            'avatar' => null,
            'role' => 'operator',
            'email' => $other->email,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertForbidden();
});

test('owner cannot change own role', function () {
    $this->actingAs($this->user)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $this->user->id]), [
            'name' => $this->user->name,
            'nickname' => null,
            'avatar' => null,
            'role' => 'admin',
            'email' => $this->user->email,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertForbidden();

    $role = $this->workspace->users()->whereKey($this->user->id)->firstOrFail()->pivot->role;
    expect($role)->toBe('owner');
});

test('owner can update own profile without changing role', function () {
    $this->actingAs($this->user)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $this->user->id]), [
            'name' => 'Owner-新名称',
            'nickname' => '老板',
            'avatar' => null,
            'role' => 'owner',
            'email' => $this->user->email,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('show-user-list', ['slug' => $this->workspaceSlug()]));

    $this->user->refresh();
    expect($this->user->name)->toBe('Owner-新名称');
});

test('can update a user with new password', function () {
    $member = User::factory()->create([
        'name' => '客服D',
        'email' => 'd@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'name' => '客服D',
            'nickname' => null,
            'avatar' => null,
            'role' => 'operator',
            'email' => 'd@example.com',
            'password' => 'newpass1234',
            'password_confirmation' => 'newpass1234',
        ])
        ->assertRedirect(route('show-user-list', ['slug' => $this->workspaceSlug()]));

    $member->refresh();
    expect(Hash::check('newpass1234', $member->password))->toBeTrue();
});

test('owner cannot set another user role to owner', function () {
    $member = User::factory()->create([
        'name' => '客服-不可升 Owner',
        'email' => 'no-owner@example.com',
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'name' => $member->name,
            'nickname' => null,
            'avatar' => null,
            'role' => 'owner',
            'email' => $member->email,
            'password' => '',
            'password_confirmation' => '',
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
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'name' => $member->name,
            'nickname' => null,
            'avatar' => null,
            'role' => 'admin',
            'email' => $member->email,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertForbidden();

    $role = $this->workspace->users()->whereKey($member->id)->firstOrFail()->pivot->role;
    expect($role)->toBe('operator');
});

test('admin cannot update another admin profile fields', function () {
    $admin = User::factory()->create([
        'name' => '管理员A',
        'email' => 'admin-a@example.com',
    ]);
    $this->workspace->users()->attach($admin->id, ['role' => 'admin']);

    $admin2 = User::factory()->create([
        'name' => '管理员B',
        'email' => 'admin-b2@example.com',
    ]);
    $this->workspace->users()->attach($admin2->id, ['role' => 'admin']);

    $this->actingAs($admin)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $admin2->id]), [
            'name' => '管理员B-新',
            'nickname' => '新昵称',
            'avatar' => 'https://example.com/a.png',
            'role' => 'admin',
            'email' => $admin2->email,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertForbidden();
});

test('admin cannot change another user password', function () {
    $admin = User::factory()->create([
        'name' => '管理员Y',
        'email' => 'admin-y@example.com',
    ]);
    $this->workspace->users()->attach($admin->id, ['role' => 'admin']);

    $member = User::factory()->create([
        'name' => '客服-密码不可被管理员改',
        'email' => 'no-admin-password-change@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $oldHash = $member->password;

    $this->actingAs($admin)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'name' => $member->name,
            'nickname' => null,
            'avatar' => null,
            'role' => 'operator',
            'email' => $member->email,
            'password' => 'newpass1234',
            'password_confirmation' => 'newpass1234',
        ])
        ->assertForbidden();

    $member->refresh();
    expect($member->password)->toBe($oldHash);
});

test('can update user online status from list', function () {
    $member = User::factory()->create([
        'name' => '客服E',
        'email' => 'e@example.com',
        'online_status' => UserOnlineStatus::OFFLINE->value,
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->put(route('update-user-online-status', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'online_status' => UserOnlineStatus::ONLINE->value,
        ])
        ->assertRedirect();

    $member->refresh();
    expect($member->online_status)->toBe(UserOnlineStatus::ONLINE->value);
});

test('can soft delete a user in current workspace', function () {
    $member = User::factory()->create([
        'name' => '客服F',
        'email' => 'f@example.com',
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->delete(route('delete-user', ['slug' => $this->workspaceSlug(), 'id' => $member->id]))
        ->assertRedirect();

    $this->assertSoftDeleted('users', ['id' => $member->id]);
});

test('cannot delete current logged in user', function () {
    $this->actingAs($this->user)
        ->delete(route('delete-user', ['slug' => $this->workspaceSlug(), 'id' => $this->user->id]))
        ->assertForbidden();
});

test('trash page shows deleted users and can restore', function () {
    $member = User::factory()->create([
        'name' => '回收站客服',
        'email' => 'trash@example.com',
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->delete(route('delete-user', ['slug' => $this->workspaceSlug(), 'id' => $member->id]))
        ->assertRedirect();

    $this->actingAs($this->user)
        ->get(route('show-user-trash-page', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('user/Trash')
            ->has('user_list', 1)
            ->has('user_list.0', fn (Assert $item) => $item
                ->hasAll(['id', 'avatar', 'name', 'email', 'role', 'deleted_at'])
                ->where('email', 'trash@example.com')
                ->etc()
            )
        );

    $this->actingAs($this->user)
        ->put(route('restore-user', ['slug' => $this->workspaceSlug(), 'id' => $member->id]))
        ->assertRedirect();

    expect($member->fresh())->not->toBeNull();
    expect($member->fresh()->deleted_at)->toBeNull();
});
