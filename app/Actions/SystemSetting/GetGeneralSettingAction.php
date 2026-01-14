<?php

namespace App\Actions\SystemSetting;

use App\Data\GeneralSettingsData;
use App\Settings\GeneralSettings;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class GetGeneralSettingAction
{
    use AsAction;

    public function __construct(
        public GeneralSettings $settings
    ) {}

    public function handle()
    {
        return GeneralSettingsData::from($this->settings);
    }

    public function asController()
    {
        return Inertia::render('admin/generalSetting/Index');
    }
}
