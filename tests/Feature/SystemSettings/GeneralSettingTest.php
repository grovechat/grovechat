<?php

use App\Data\GeneralSettingsData;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
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
        ->get(route('system-setting.get-general-settings', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('systemSettings/GeneralSetting'));
});

test('unauthenticated user cannot view general settings page', function () {
    get(route('system-setting.get-general-settings', ['slug' => $this->workspaceSlug()]))
        ->assertRedirect('/login');
});

test('authenticated user can update general settings with all fields', function () {
    $response = actingAs($this->user)
        ->put(route('system-setting.update-general-settings', ['slug' => $this->workspaceSlug()]), [
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
});

test('authenticated user can update general settings with required fields only', function () {
    actingAs($this->user)
        ->put(route('system-setting.update-general-settings', ['slug' => $this->workspaceSlug()]), [
            'base_url' => 'https://app.grovechat.com',
            'name' => 'GroveChat',
        ])
        ->assertRedirect();

    $settings = app(GeneralSettings::class);
    expect($settings->base_url)->toBe('https://app.grovechat.com');
    expect($settings->name)->toBe('GroveChat');
});

test('baseUrl is required', function () {
    try {
        GeneralSettingsData::validateAndCreate([
            'name' => '客服系统',
        ]);
        $this->fail('Expected ValidationException for missing baseUrl.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('baseUrl');
    }
});

test('name is required', function () {
    try {
        GeneralSettingsData::validateAndCreate([
            'baseUrl' => 'https://app.grovechat.com',
        ]);
        $this->fail('Expected ValidationException for missing name.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('name');
    }
});

test('baseUrl must be valid url', function () {
    try {
        GeneralSettingsData::validateAndCreate([
            'baseUrl' => 'not-a-valid-url',
            'name' => 'GroveChat',
        ]);
        $this->fail('Expected ValidationException for invalid baseUrl.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('baseUrl');
    }
});

test('name cannot exceed 255 characters', function () {
    try {
        GeneralSettingsData::validateAndCreate([
            'baseUrl' => 'https://app.grovechat.com',
            'name' => str_repeat('a', 256),
        ]);
        $this->fail('Expected ValidationException for name max length.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('name');
    }
});

test('unauthenticated user cannot update general settings', function () {
    put(route('system-setting.update-general-settings', ['slug' => $this->workspaceSlug()]), [
        'base_url' => 'https://app.grovechat.com',
        'name' => 'GroveChat',
    ])
        ->assertRedirect('/login');
});

test('logoId cannot exceed 500 characters', function () {
    try {
        GeneralSettingsData::validateAndCreate([
            // validateAndCreate 在启用 input/output SnakeCaseMapper 时，字段名可能会出现歧义
            // 这里同时传 baseUrl/base_url 与 logoId/logo_id，保证规则命中
            'baseUrl' => 'https://app.grovechat.com',
            'base_url' => 'https://app.grovechat.com',
            'name' => 'GroveChat',
            'logoId' => str_repeat('a', 501),
            'logo_id' => str_repeat('a', 501),
        ]);
        $this->fail('Expected ValidationException for logoId max length.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('logoId');
    }
});
