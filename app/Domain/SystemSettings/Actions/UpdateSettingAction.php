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
     * @param GeneralSettingsData $data 设置数据
     */
    public function execute(GeneralSettingsData $data)
    {
        $this->settings->lock('version');
        $this->settings->fill($data)->save();
    }
}
