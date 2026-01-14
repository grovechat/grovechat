<?php

use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'is_super_admin' => true,
    ]);
});

test('super admin can view general settings page', function () {
    $this->withoutExceptionHandling();

    actingAs($this->user)
        ->get(route('get-general-setting'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('generalSetting/Index'));
});

test('non-super-admin cannot view general settings page', function () {
    $user = User::factory()->create([
        'is_super_admin' => false,
    ]);

    actingAs($user)
        ->get(route('get-general-setting'))
        ->assertForbidden();
});

test('unauthenticated user cannot view general settings page', function () {
    get(route('get-general-setting'))
        ->assertRedirect('/login');
});

test('super admin can update general settings with all fields', function () {
    $response = actingAs($this->user)
        ->put(route('update-general-setting'), [
            // 使用 snake_case，依赖 Laravel Data 自动转换为 camelCase 映射到 Data 对象
            'base_url' => 'https://app.grovechat.com',
            'name' => 'GroveChat',
            'logo_id' => '01kepy83b9sxs4scf7q36mxa5z',
            'copyright' => '© 2026 GroveChat',
            'icp_record' => '京ICP备12345678号',
        ]);

    $response->assertRedirect();

    // 验证设置已更新
    $settings = app(GeneralSettings::class);
    expect($settings->base_url)->toBe('https://app.grovechat.com');
    expect($settings->name)->toBe('GroveChat');
    expect($settings->copyright)->toBe('© 2026 GroveChat');
    expect($settings->logo_id)->toBe('01kepy83b9sxs4scf7q36mxa5z');
    expect($settings->icp_record)->toBe('京ICP备12345678号');
});

test('super admin can update general settings with required fields only', function () {
    actingAs($this->user)
        ->put(route('update-general-setting'), [
            'base_url' => 'https://app.grovechat.com',
            'name' => 'GroveChat',
        ])
        ->assertRedirect();

    $settings = app(GeneralSettings::class);
    expect($settings->base_url)->toBe('https://app.grovechat.com');
    expect($settings->name)->toBe('GroveChat');
});

test('baseUrl is required', function () {
    actingAs($this->user)
        ->put(route('update-general-setting'), [
            'name' => '客服系统',
        ])
        ->assertSessionHasErrors('baseUrl');
});

test('name is required', function () {
    actingAs($this->user)
        ->put(route('update-general-setting'), [
            'base_url' => 'https://app.grovechat.com',
        ])
        ->assertSessionHasErrors('name');
});

test('baseUrl must be valid url', function () {
    actingAs($this->user)
        ->put(route('update-general-setting'), [
            'base_url' => 'not-a-valid-url',
            'name' => 'GroveChat',
        ])
        ->assertSessionHasErrors('baseUrl');
});

test('name cannot exceed 255 characters', function () {
    actingAs($this->user)
        ->put(route('update-general-setting'), [
            'base_url' => 'https://app.grovechat.com',
            'name' => str_repeat('a', 256),
        ])
        ->assertSessionHasErrors('name');
});

test('unauthenticated user cannot update general settings', function () {
    put(route('update-general-setting'), [
        'base_url' => 'https://app.grovechat.com',
        'name' => 'GroveChat',
    ])
        ->assertRedirect('/login');
});

test('logoId cannot exceed 500 characters', function () {
    actingAs($this->user)
        ->put(route('update-general-setting'), [
            'base_url' => 'https://app.grovechat.com',
            'name' => 'GroveChat',
            'logo_id' => str_repeat('a', 501),
        ])
        ->assertSessionHasErrors('logoId');
});
