<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    // 验证注册时自动创建了租户
    $user = auth()->user();
    expect($user->workspaces()->count())->toBe(1);

    // 注册后会重定向到全局 dashboard，然后再重定向到租户 dashboard
    // 所以这里检查重定向到 dashboard 路由即可
    $response->assertRedirect(route('dashboard', absolute: false));
});
