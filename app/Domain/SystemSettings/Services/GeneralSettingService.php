<?php

namespace App\Domain\SystemSettings\Services;

use App\Settings\GeneralSettings;

class GeneralSettingService
{
    public function __construct(
        private GeneralSettings $settings
    ) {}

    /**
     * 更新通用设置
     *
     * @param array $data 验证后的数据
     * @return bool
     */
    public function updateSettings(array $data): bool
    {
        $this->settings->baseUrl = $data['baseUrl'];
        $this->settings->name = $data['name'];
        $this->settings->logo = $data['logo'] ?? null;
        $this->settings->copyright = $data['copyright'] ?? null;
        $this->settings->icpRecord = $data['icpRecord'] ?? null;
        
        $this->settings->save();
        
        return true;
    }

    /**
     * 获取所有设置
     *
     * @return GeneralSettings
     */
    public function getSettings(): GeneralSettings
    {
        return $this->settings;
    }
}
