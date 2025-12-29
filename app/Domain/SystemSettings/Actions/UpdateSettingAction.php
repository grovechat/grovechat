<?php

namespace App\Domain\SystemSettings\Actions;

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
        $this->settings->baseUrl = $data->baseUrl;
        $this->settings->name = $data->name;
        $this->settings->logo = $data->logo;
        $this->settings->copyright = $data->copyright;
        $this->settings->icpRecord = $data->icpRecord;
        
        $this->settings->save();
    
        return $this->settings;
    }
}