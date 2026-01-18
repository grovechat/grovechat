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

    $this->teammateUpdatePayload = fn (User $target, string $role, array $overrides = []) => array_merge([
        'name' => $target->name,
        'nickname' => null,
        'avatar' => null,
        'role' => $role,
        'email' => $target->email,
        'password' => '',
        'password_confirmation' => '',
    ], $overrides);
});

test('authenticated user can view user list page', function () {
    $member = User::factory()->create([
        'name' => '客服A',
        'email' => 'a@example.com',
        'online_status' => UserOnlineStatus::OFFLINE->value,
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->get(route('show-teammate-list', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('teammate/List')
            ->has('user_list')
            ->has('user_list.0', fn (Assert $item) => $item
                ->hasAll(['user_id', 'user_name', 'user_avatar', 'user_email', 'role', 'user_online_status', 'show_delete_button'])
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

test('authenticated user can view create user page with role options', function () {
    $this->actingAs($this->user)
        ->get(route('show-create-teammate-page', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('teammate/Create')
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

test('can create a user in current workspace', function () {
    $this->actingAs($this->user)
        ->post(route('create-teammate', ['slug' => $this->workspaceSlug()]), [
            'name' => '客服B',
            'nickname' => '小B',
            'avatar' => 'https://example.com/a.png',
            'role' => 'operator',
            'email' => 'b@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])
        ->assertRedirect(route('show-teammate-list', ['slug' => $this->workspaceSlug()]));

    $created = User::query()->where('email', 'b@example.com')->firstOrFail();
    expect(Hash::check('secret1234', $created->password))->toBeTrue();
    expect($this->workspace->users()->whereKey($created->id)->exists())->toBeTrue();
});

test('cannot create a user with owner role', function () {
    $this->actingAs($this->user)
        ->post(route('create-teammate', ['slug' => $this->workspaceSlug()]), [
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
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'name' => '客服C-新',
            'nickname' => '小C',
            'avatar' => null,
            'role' => 'admin',
            'email' => 'c@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('show-teammate-list', ['slug' => $this->workspaceSlug()]));

    $member->refresh();
    expect($member->name)->toBe('客服C-新');
    expect($member->password)->toBe($oldHash);
});

test('owner and admin cannot update own email', function (string $role) {
    $actor = match ($role) {
        'owner' => $this->user,
        'admin' => User::factory()->create([
            'name' => '管理员A',
            'email' => 'admin-a@example.com',
        ]),
        default => throw new InvalidArgumentException('Unsupported role.'),
    };

    if ($role !== 'owner') {
        $this->workspace->users()->attach($actor->id, ['role' => $role]);
    }

    $originalEmail = $actor->email;

    $this->actingAs($actor)
        ->put(
            route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $actor->id]),
            ($this->teammateUpdatePayload)($actor, $role, ['email' => fake()->unique()->safeEmail()])
        )
        ->assertForbidden();

    expect($actor->refresh()->email)->toBe($originalEmail);
})->with(['owner', 'admin']);

test('owner can update another user email', function () {
    $member = User::factory()->create([
        'name' => '客服E1',
        'email' => 'e1@example.com',
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'name' => '客服E1-新',
            'nickname' => '新昵称',
            'avatar' => 'https://example.com/e1.png',
            'role' => 'operator',
            'email' => 'e1-new@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('show-teammate-list', ['slug' => $this->workspaceSlug()]));

    $member->refresh();
    expect($member->email)->toBe('e1-new@example.com');
    expect($member->name)->toBe('客服E1-新');
    expect($member->nickname)->toBe('新昵称');
    expect($member->avatar)->toBe('https://example.com/e1.png');
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
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $operator->id]), [
            'name' => '客服P-新',
            'nickname' => '新昵称',
            'avatar' => 'https://example.com/a.png',
            'role' => 'operator',
            'email' => $operator->email,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('show-teammate-list', ['slug' => $this->workspaceSlug()]));

    $operator->refresh();
    expect($operator->name)->toBe('客服P-新');
    expect($operator->nickname)->toBe('新昵称');
    expect($operator->avatar)->toBe('https://example.com/a.png');

    $this->actingAs($admin)
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $operator->id]), [
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

test('admin cannot update another admin', function () {
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
        ->put(
            route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $admin2->id]),
            ($this->teammateUpdatePayload)($admin2, 'admin', [
                'name' => '管理员2-新',
                'nickname' => '新昵称',
                'avatar' => 'https://example.com/a.png',
                'email' => fake()->unique()->safeEmail(),
                'password' => 'newpass1234',
                'password_confirmation' => 'newpass1234',
            ])
        )
        ->assertForbidden();
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
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $operator->id]), [
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
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $other->id]), [
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
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $this->user->id]), [
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
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $this->user->id]), [
            'name' => 'Owner-新名称',
            'nickname' => '老板',
            'avatar' => null,
            'role' => 'owner',
            'email' => $this->user->email,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('show-teammate-list', ['slug' => $this->workspaceSlug()]));

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
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'name' => '客服D',
            'nickname' => null,
            'avatar' => null,
            'role' => 'operator',
            'email' => 'd@example.com',
            'password' => 'newpass1234',
            'password_confirmation' => 'newpass1234',
        ])
        ->assertRedirect(route('show-teammate-list', ['slug' => $this->workspaceSlug()]));

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
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
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
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
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
        ->put(route('update-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
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
        ->put(route('update-teammate-online-status', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'online_status' => UserOnlineStatus::ONLINE->value,
        ])
        ->assertRedirect();

    $member->refresh();
    expect($member->online_status)->toBe(UserOnlineStatus::ONLINE);
});

test('cannot delete current logged in user', function () {
    $this->actingAs($this->user)
        ->delete(route('delete-teammate', ['slug' => $this->workspaceSlug(), 'id' => $this->user->id]))
        ->assertForbidden();
});

test('trash page shows deleted users and can restore', function () {
    $member = User::factory()->create([
        'name' => '回收站客服',
        'email' => 'trash@example.com',
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'operator']);

    $this->actingAs($this->user)
        ->delete(route('delete-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]))
        ->assertRedirect();

    $this->assertSoftDeleted('users', ['id' => $member->id]);

    $this->actingAs($this->user)
        ->get(route('show-teammate-trash-page', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('teammate/Trash')
            ->has('user_list', 1)
            ->has('user_list.0', fn (Assert $item) => $item
                ->hasAll(['id', 'avatar', 'name', 'email', 'role', 'deleted_at'])
                ->where('email', 'trash@example.com')
                ->etc()
            )
        );

    $this->actingAs($this->user)
        ->put(route('restore-teammate', ['slug' => $this->workspaceSlug(), 'id' => $member->id]))
        ->assertRedirect();

    expect($member->fresh())->not->toBeNull();
    expect($member->fresh()->deleted_at)->toBeNull();
});
