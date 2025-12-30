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
     * @param array $data 请求数据
     */
    public function execute(array $data)
    {
        $data = GeneralSettingsData::from($data);
        $this->settings->lock('version');
        $this->settings->fill($data)->save();    
    }
}
