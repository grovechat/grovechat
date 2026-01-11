<?php

use App\Models\User;
use App\Models\Workspace;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $user->workspaces()->attach($workspace, ['role' => 'owner']);

    $this->actingAs($user);

    // 访问全局 dashboard 会重定向到租户 dashboard
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('workspace.dashboard', ['workspace_path' => $workspace->path]));

    // 访问租户 dashboard 应该成功
    $response = $this->get(route('workspace.dashboard', ['workspace_path' => $workspace->path]));
    $response->assertStatus(200);
});
