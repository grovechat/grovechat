<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('password update page is displayed', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant, ['role' => 'owner']);

    $response = $this
        ->actingAs($user)
        ->get(route('user-password.edit', ['tenant_path' => $tenant->path]));

    $response->assertStatus(200);
});

test('password can be updated', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant, ['role' => 'owner']);

    $response = $this
        ->actingAs($user)
        ->from(route('user-password.edit', ['tenant_path' => $tenant->path]))
        ->put(route('user-password.update', ['tenant_path' => $tenant->path]), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('user-password.edit', ['tenant_path' => $tenant->path]));

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant, ['role' => 'owner']);

    $response = $this
        ->actingAs($user)
        ->from(route('user-password.edit', ['tenant_path' => $tenant->path]))
        ->put(route('user-password.update', ['tenant_path' => $tenant->path]), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrors('current_password')
        ->assertRedirect(route('user-password.edit', ['tenant_path' => $tenant->path]));
});