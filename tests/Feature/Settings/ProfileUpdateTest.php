<?php

use Tests\WithWorkspace;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class, WithWorkspace::class);

beforeEach(function () {
    $this->user = $this->createUserWithWorkspace();
});

test('profile page is displayed', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('profile.edit', ['slug' => $this->workspaceSlug()]));

    $response->assertOk();
});

test('profile information can be updated', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch(route('profile.update', ['slug' => $this->workspaceSlug()]), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit', ['slug' => $this->workspaceSlug()]));

    $this->user->refresh();

    expect($this->user->name)->toBe('Test User');
    expect($this->user->email)->toBe('test@example.com');
    expect($this->user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch(route('profile.update', ['slug' => $this->workspaceSlug()]), [
            'name' => 'Test User',
            'email' => $this->user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit', ['slug' => $this->workspaceSlug()]));

    expect($this->user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $response = $this
        ->actingAs($this->user)
        ->delete(route('profile.destroy', ['slug' => $this->workspaceSlug()]), [
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
        ->from(route('profile.edit', ['slug' => $this->workspaceSlug()]))
        ->delete(route('profile.destroy', ['slug' => $this->workspaceSlug()]), [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('profile.edit', ['slug' => $this->workspaceSlug()]));

    expect($this->user->fresh())->not->toBeNull();
});
