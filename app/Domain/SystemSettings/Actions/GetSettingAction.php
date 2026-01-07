<?php

namespace App\Domain\SystemSettings\Actions;

use App\Settings\GeneralSettings;
use App\Domain\SystemSettings\DTOs\GeneralSettingsData;

class GetSettingAction
{
    public function __construct(
        private GeneralSettings $settings
    ) {}

    /**
     * 执行获取设置的操作
     *
     */
    public function execute()
    {
        return GeneralSettingsData::from($this->settings)->toArray();
    }
}
