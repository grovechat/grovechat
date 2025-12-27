<?php

use App\Domain\SystemSettings\Services\GeneralSettingService;
use App\Settings\GeneralSettings;

test('it can update all settings', function () {
    // 创建 Mock 对象
    $mockSettings = Mockery::mock(GeneralSettings::class);

    // 准备测试数据
    $data = [
        'baseUrl' => 'https://app.grovechat.com',
        'name' => 'GroveChat',
        'logo' => 'logo.png',
        'copyright' => '© 2026 GroveChat',
        'icpRecord' => '京ICP备12345678号',
    ];

    // 设置 Mock 期望 - save() 返回对象本身
    $mockSettings->shouldReceive('save')->once()->andReturnSelf();

    // 创建 Service 实例
    $service = new GeneralSettingService($mockSettings);

    // 执行测试
    $result = $service->updateSettings($data);

    // 断言
    expect($result)->toBeTrue();
    expect($mockSettings->baseUrl)->toBe('https://app.grovechat.com');
    expect($mockSettings->name)->toBe('GroveChat');
    expect($mockSettings->logo)->toBe('logo.png');
    expect($mockSettings->copyright)->toBe('© 2026 GroveChat');
    expect($mockSettings->icpRecord)->toBe('京ICP备12345678号');

    Mockery::close();
});

test('it can update required settings only', function () {
    // 创建 Mock 对象
    $mockSettings = Mockery::mock(GeneralSettings::class);

    // 只提供必填字段
    $data = [
        'baseUrl' => 'https://app.grovechat.com',
        'name' => 'GroveChat',
        'logo' => null,
        'copyright' => null,
        'icpRecord' => null,
    ];

    $mockSettings->shouldReceive('save')->once()->andReturnSelf();

    $service = new GeneralSettingService($mockSettings);
    $result = $service->updateSettings($data);

    expect($result)->toBeTrue();
    expect($mockSettings->baseUrl)->toBe('https://app.grovechat.com');
    expect($mockSettings->name)->toBe('GroveChat');
    expect($mockSettings->logo)->toBeNull();
    expect($mockSettings->copyright)->toBeNull();
    expect($mockSettings->icpRecord)->toBeNull();

    Mockery::close();
});

test('it can get settings', function () {
    // 创建 Mock 对象
    $mockSettings = Mockery::mock(GeneralSettings::class);

    $service = new GeneralSettingService($mockSettings);
    $settings = $service->getSettings();

    // 断言返回的是 GeneralSettings 实例
    expect($settings)->toBeInstanceOf(GeneralSettings::class);

    Mockery::close();
});
