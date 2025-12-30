<?php

namespace App\Domain\SystemSettings\Actions;

use App\Domain\SystemSettings\DTOs\GeneralSettingsData;
use App\Settings\GeneralSettings;

class UpdateSettingAction
{
    public function __construct(
        private GeneralSettings $settings
    ) {}

    /**
     * 执行更新设置的操作
     *
     * @param GeneralSettingsData $data 验证后的数据
     * @return bool
     */
    public function execute(GeneralSettingsData $data): GeneralSettings
    {
        $this->settings->fill($data)->save();

        return $this->settings;
    }
}
