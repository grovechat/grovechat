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
    $this->workspace->users()->attach($member->id, ['role' => 'customer_service']);

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

test('can create a user in current workspace', function () {
    $this->actingAs($this->user)
        ->post(route('create-user', ['slug' => $this->workspaceSlug()]), [
            'name' => '客服B',
            'external_nickname' => '小B',
            'avatar' => 'https://example.com/a.png',
            'role' => 'customer_service',
            'email' => 'b@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])
        ->assertRedirect(route('show-user-list', ['slug' => $this->workspaceSlug()]));

    $created = User::query()->where('email', 'b@example.com')->firstOrFail();
    expect(Hash::check('secret1234', $created->password))->toBeTrue();
    expect($this->workspace->users()->whereKey($created->id)->exists())->toBeTrue();
});

test('can update a user without changing password', function () {
    $member = User::factory()->create([
        'name' => '客服C',
        'email' => 'c@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'customer_service']);

    $oldHash = $member->password;

    $this->actingAs($this->user)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'name' => '客服C-新',
            'external_nickname' => '小C',
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

test('can update a user with new password', function () {
    $member = User::factory()->create([
        'name' => '客服D',
        'email' => 'd@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'customer_service']);

    $this->actingAs($this->user)
        ->put(route('update-user', ['slug' => $this->workspaceSlug(), 'id' => $member->id]), [
            'name' => '客服D',
            'external_nickname' => null,
            'avatar' => null,
            'role' => 'customer_service',
            'email' => 'd@example.com',
            'password' => 'newpass1234',
            'password_confirmation' => 'newpass1234',
        ])
        ->assertRedirect(route('show-user-list', ['slug' => $this->workspaceSlug()]));

    $member->refresh();
    expect(Hash::check('newpass1234', $member->password))->toBeTrue();
});

test('can update user online status from list', function () {
    $member = User::factory()->create([
        'name' => '客服E',
        'email' => 'e@example.com',
        'online_status' => UserOnlineStatus::OFFLINE->value,
    ]);
    $this->workspace->users()->attach($member->id, ['role' => 'customer_service']);

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
    $this->workspace->users()->attach($member->id, ['role' => 'customer_service']);

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
