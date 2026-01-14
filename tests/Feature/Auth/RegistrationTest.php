<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('registration screen can be rendered', function () {
    $response = get(route('register'));

    $response->assertStatus(200);
});

test('first registered user is super admin and will be redirected to system settings', function () {
    $response = post(route('register.store'), [
        'name' => 'adminuser',
        'email' => 'admin@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    expect(Auth::check())->toBeTrue();

    $user = \App\Models\User::query()->findOrFail(Auth::id());
    expect($user->is_super_admin)->toBeTrue();
    expect($user->workspaces()->count())->toBe(0);

    $response->assertRedirect('/admin');
});

test('new users can register', function () {
    \App\Models\User::factory()->create();

    $response = post(route('register.store'), [
        'name' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    expect(Auth::check())->toBeTrue();

    // 验证注册时自动创建了租户
    $user = \App\Models\User::query()->findOrFail(Auth::id());
    expect($user->workspaces()->count())->toBe(1);

    // 注册后会重定向到全局 dashboard，然后再重定向到租户 dashboard
    // 所以这里检查重定向到 dashboard 路由即可
    $response->assertRedirect(route('dashboard', absolute: false));
});
