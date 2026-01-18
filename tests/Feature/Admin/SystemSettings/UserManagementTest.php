<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::factory()->create([
        'is_super_admin' => true,
    ]);
});

test('super admin can view system user list page', function () {
    $user = User::factory()->create([
        'is_super_admin' => false,
        'name' => '普通用户A',
        'email' => 'u-a@example.com',
        'two_factor_confirmed_at' => now(),
        'avatar' => 'https://example.com/a.png',
    ]);

    $super = User::factory()->create([
        'is_super_admin' => true,
        'name' => '超级管理员',
        'email' => 'sa@example.com',
    ]);

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.get-user-list'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/user/List')
            ->has('user_list')
            ->has('user_list_pagination')
            ->where('user_list_pagination.current_page', 1)
            ->where('user_list.0.email', $user->email)
            ->where('user_list.0.two_factor_enabled', true)
            ->etc()
        );

    expect(User::query()->whereKey($super->id)->exists())->toBeTrue();
});

test('non-super-admin cannot access system user pages', function () {
    $user = User::factory()->create([
        'is_super_admin' => false,
    ]);

    $this->actingAs($user, 'admin')
        ->get(route('admin.get-user-list'))
        ->assertForbidden();
});

test('unauthenticated user cannot access system user pages', function () {
    $this->get(route('admin.get-user-list'))
        ->assertRedirect('/login');
});

test('super admin can create a non-super-admin user', function () {
    $this->actingAs($this->superAdmin, 'admin')
        ->post(route('admin.create-user'), [
            'name' => '新用户',
            'email' => 'new-user@example.com',
            'avatar' => 'https://example.com/u.png',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])
        ->assertRedirect(route('admin.get-user-list'));

    $created = User::query()->where('email', 'new-user@example.com')->firstOrFail();
    expect($created->is_super_admin)->toBeFalse();
    expect(Hash::check('secret1234', $created->password))->toBeTrue();
});

test('super admin can update a user without changing password', function () {
    $user = User::factory()->create([
        'is_super_admin' => false,
        'name' => '旧名',
        'email' => 'u-edit@example.com',
        'password' => Hash::make('old-password'),
    ]);

    $oldHash = $user->password;

    $this->actingAs($this->superAdmin, 'admin')
        ->put(route('admin.update-user', ['id' => $user->id]), [
            'name' => '新名',
            'email' => 'u-edit@example.com',
            'avatar' => null,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('admin.get-user-list'));

    $user->refresh();
    expect($user->name)->toBe('新名');
    expect($user->password)->toBe($oldHash);
});

test('super admin cannot edit a super admin via system user routes', function () {
    $target = User::factory()->create([
        'is_super_admin' => true,
        'email' => 'sa2@example.com',
    ]);

    $this->actingAs($this->superAdmin, 'admin')
        ->get(route('admin.show-edit-user-page', ['id' => $target->id]))
        ->assertNotFound();
});
