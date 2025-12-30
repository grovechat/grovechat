<?php

use App\Models\Tenant;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant, ['role' => 'owner']);
    
    $this->actingAs($user);

    // 访问全局 dashboard 会重定向到租户 dashboard
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('tenant.dashboard', ['tenant_path' => $tenant->path]));
    
    // 访问租户 dashboard 应该成功
    $response = $this->get(route('tenant.dashboard', ['tenant_path' => $tenant->path]));
    $response->assertStatus(200);
});