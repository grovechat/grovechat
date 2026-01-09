<?php

use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\WithWorkspace;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

uses(RefreshDatabase::class, WithWorkspace::class);

beforeEach(function () {
    // 每个测试前创建认证用户和租户
    $this->user = $this->createUserWithWorkspace();
});

test('authenticated user can view general settings page', function () {
    $this->withoutExceptionHandling();

    actingAs($this->user)
        ->get(route('system-setting.get-general-settings', ['workspace_path' => $this->workspacePath()]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('systemSettings/GeneralSetting'));
});

test('unauthenticated user cannot view general settings page', function () {
    get(route('system-setting.get-general-settings', ['workspace_path' => $this->workspacePath()]))
        ->assertRedirect('/login');
});

test('authenticated user can update general settings with all fields', function () {
    $response = actingAs($this->user)
        ->put(route('system-setting.update-general-settings', ['workspace_path' => $this->workspacePath()]), [
            'baseUrl' => 'https://app.grovechat.com',
            'name' => 'GroveChat',
            'logo' => 'https://cdn.example.com/logo.png',
            'copyright' => '© 2026 GroveChat',
            'icpRecord' => '京ICP备12345678号',
        ]);

    $response->assertRedirect();

    // 验证设置已更新
    $settings = app(GeneralSettings::class);
    expect($settings->baseUrl)->toBe('https://app.grovechat.com');
    expect($settings->name)->toBe('GroveChat');
    expect($settings->logo)->toBe('https://cdn.example.com/logo.png');
    expect($settings->copyright)->toBe('© 2026 GroveChat');
    expect($settings->icpRecord)->toBe('京ICP备12345678号');
});

test('authenticated user can update general settings with required fields only', function () {
    actingAs($this->user)
        ->put(route('system-setting.update-general-settings', ['workspace_path' => $this->workspacePath()]), [
            'baseUrl' => 'https://app.grovechat.com',
            'name' => 'GroveChat',
        ])
        ->assertRedirect();

    $settings = app(GeneralSettings::class);
    expect($settings->baseUrl)->toBe('https://app.grovechat.com');
    expect($settings->name)->toBe('GroveChat');
});

test('baseUrl is required', function () {
    actingAs($this->user)
        ->put(route('system-setting.update-general-settings', ['workspace_path' => $this->workspacePath()]), [
            'name' => '客服系统',
        ])
        ->assertSessionHasErrors('baseUrl');
});

test('name is required', function () {
    actingAs($this->user)
        ->put(route('system-setting.update-general-settings', ['workspace_path' => $this->workspacePath()]), [
            'baseUrl' => 'https://app.grovechat.com',
        ])
        ->assertSessionHasErrors('name');
});

test('baseUrl must be valid url', function () {
    actingAs($this->user)
        ->put(route('system-setting.update-general-settings', ['workspace_path' => $this->workspacePath()]), [
            'baseUrl' => 'not-a-valid-url',
            'name' => 'GroveChat',
        ])
        ->assertSessionHasErrors('baseUrl');
});

test('name cannot exceed 255 characters', function () {
    actingAs($this->user)
        ->put(route('system-setting.update-general-settings', ['workspace_path' => $this->workspacePath()]), [
            'baseUrl' => 'https://app.grovechat.com',
            'name' => str_repeat('a', 256),
        ])
        ->assertSessionHasErrors('name');
});

test('unauthenticated user cannot update general settings', function () {
    put(route('system-setting.update-general-settings', ['workspace_path' => $this->workspacePath()]), [
        'baseUrl' => 'https://app.grovechat.com',
        'name' => 'GroveChat',
    ])
    ->assertRedirect('/login');
});

test('logo url cannot exceed 500 characters', function () {
    actingAs($this->user)
        ->put(route('system-setting.update-general-settings', ['workspace_path' => $this->workspacePath()]), [
            'baseUrl' => 'https://app.grovechat.com',
            'name' => 'GroveChat',
            'logo' => 'https://example.com/' . str_repeat('a', 500),
        ])
        ->assertSessionHasErrors('logo');
});
