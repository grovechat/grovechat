<?php

use App\Models\Tenant;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('profile page is displayed', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant, ['role' => 'owner']);

    $response = $this
        ->actingAs($user)
        ->get(route('profile.edit', ['tenant_path' => $tenant->path]));

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant, ['role' => 'owner']);

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update', ['tenant_path' => $tenant->path]), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit', ['tenant_path' => $tenant->path]));

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant, ['role' => 'owner']);

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update', ['tenant_path' => $tenant->path]), [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit', ['tenant_path' => $tenant->path]));

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant, ['role' => 'owner']);

    $response = $this
        ->actingAs($user)
        ->delete(route('profile.destroy', ['tenant_path' => $tenant->path]), [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant, ['role' => 'owner']);

    $response = $this
        ->actingAs($user)
        ->from(route('profile.edit', ['tenant_path' => $tenant->path]))
        ->delete(route('profile.destroy', ['tenant_path' => $tenant->path]), [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('profile.edit', ['tenant_path' => $tenant->path]));

    expect($user->fresh())->not->toBeNull();
});