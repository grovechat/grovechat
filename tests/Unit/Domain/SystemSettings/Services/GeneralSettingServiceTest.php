<?php

namespace Tests\Unit\Domain\SystemSettings\Services;

use App\Domain\SystemSettings\Services\GeneralSettingService;
use App\Settings\GeneralSettings;
use Mockery;
use Tests\TestCase;

class GeneralSettingServiceTest extends TestCase
{
    private GeneralSettingService $service;
    private GeneralSettings $mockSettings;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 创建 Mock 对象，避免真实数据库操作
        $this->mockSettings = Mockery::mock(GeneralSettings::class);
        $this->service = new GeneralSettingService($this->mockSettings);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_update_all_settings()
    {
        // 准备测试数据
        $data = [
            'baseUrl' => 'https://example.com',
            'name' => '客服系统',
            'logo' => 'logo.png',
            'copyright' => '© 2025 公司',
            'icpRecord' => '京ICP备12345678号',
        ];

        // 设置 Mock 期望
        $this->mockSettings->shouldReceive('getAttribute')->andReturnSelf();
        $this->mockSettings->shouldReceive('save')->once()->andReturn(true);

        // 执行测试
        $result = $this->service->updateSettings($data);

        // 断言
        $this->assertTrue($result);
        $this->assertEquals('https://example.com', $this->mockSettings->baseUrl);
        $this->assertEquals('客服系统', $this->mockSettings->name);
        $this->assertEquals('logo.png', $this->mockSettings->logo);
        $this->assertEquals('© 2025 公司', $this->mockSettings->copyright);
        $this->assertEquals('京ICP备12345678号', $this->mockSettings->icpRecord);
    }

    /** @test */
    public function it_can_update_required_settings_only()
    {
        // 只提供必填字段
        $data = [
            'baseUrl' => 'https://test.com',
            'name' => '测试系统',
            'logo' => null,
            'copyright' => null,
            'icpRecord' => null,
        ];

        $this->mockSettings->shouldReceive('getAttribute')->andReturnSelf();
        $this->mockSettings->shouldReceive('save')->once()->andReturn(true);

        $result = $this->service->updateSettings($data);

        $this->assertTrue($result);
        $this->assertEquals('https://test.com', $this->mockSettings->baseUrl);
        $this->assertEquals('测试系统', $this->mockSettings->name);
        $this->assertNull($this->mockSettings->logo);
        $this->assertNull($this->mockSettings->copyright);
        $this->assertNull($this->mockSettings->icpRecord);
    }

    /** @test */
    public function it_can_get_settings()
    {
        // 执行测试
        $settings = $this->service->getSettings();

        // 断言返回的是 GeneralSettings 实例
        $this->assertInstanceOf(GeneralSettings::class, $settings);
    }
}
