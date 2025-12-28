<?php

use Tests\WithTenant;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class, WithTenant::class);

beforeEach(function () {
    $this->user = $this->createUserWithTenant();
});

test('profile page is displayed', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('profile.edit', ['tenant_path' => $this->tenantPath()]));

    $response->assertOk();
});

test('profile information can be updated', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch(route('profile.update', ['tenant_path' => $this->tenantPath()]), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit', ['tenant_path' => $this->tenantPath()]));

    $this->user->refresh();

    expect($this->user->name)->toBe('Test User');
    expect($this->user->email)->toBe('test@example.com');
    expect($this->user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch(route('profile.update', ['tenant_path' => $this->tenantPath()]), [
            'name' => 'Test User',
            'email' => $this->user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit', ['tenant_path' => $this->tenantPath()]));

    expect($this->user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $response = $this
        ->actingAs($this->user)
        ->delete(route('profile.destroy', ['tenant_path' => $this->tenantPath()]), [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertGuest();
    expect($this->user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $response = $this
        ->actingAs($this->user)
        ->from(route('profile.edit', ['tenant_path' => $this->tenantPath()]))
        ->delete(route('profile.destroy', ['tenant_path' => $this->tenantPath()]), [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('profile.edit', ['tenant_path' => $this->tenantPath()]));

    expect($this->user->fresh())->not->toBeNull();
});