<?php

use Illuminate\Support\Facades\Hash;
use Tests\WithTenant;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class, WithTenant::class);

beforeEach(function () {
    $this->user = $this->createUserWithTenant();
});

test('password update page is displayed', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('user-password.edit', ['tenant_path' => $this->tenantPath()]));

    $response->assertStatus(200);
});

test('password can be updated', function () {
    $response = $this
        ->actingAs($this->user)
        ->from(route('user-password.edit', ['tenant_path' => $this->tenantPath()]))
        ->put(route('user-password.update', ['tenant_path' => $this->tenantPath()]), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('user-password.edit', ['tenant_path' => $this->tenantPath()]));

    expect(Hash::check('new-password', $this->user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $response = $this
        ->actingAs($this->user)
        ->from(route('user-password.edit', ['tenant_path' => $this->tenantPath()]))
        ->put(route('user-password.update', ['tenant_path' => $this->tenantPath()]), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrors('current_password')
        ->assertRedirect(route('user-password.edit', ['tenant_path' => $this->tenantPath()]));
});