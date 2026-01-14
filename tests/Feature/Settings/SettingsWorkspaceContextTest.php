<?php

use App\Models\User;
use Tests\WithWorkspace;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class, WithWorkspace::class);

test('normal user is redirected to dashboard when accessing settings without from_workspace', function () {
    $user = $this->createUserWithWorkspace();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertRedirect(route('dashboard'));
});

test('normal user gets not found when accessing settings with invalid from_workspace', function () {
    $user = $this->createUserWithWorkspace();

    $this->actingAs($user)
        ->get(route('profile.edit', ['from_workspace' => 'not-exists']))
        ->assertNotFound();
});

test('super admin can access settings without from_workspace', function () {
    $user = User::factory()->create([
        'is_super_admin' => true,
    ]);

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk();
});
