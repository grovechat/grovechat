<?php

/**
 * 系统设置
 */

namespace App\Http\Controllers;

use App\Domain\SystemSettings\DTOs\GeneralSettingsData;
use App\Domain\SystemSettings\Actions\UpdateSettingAction;
use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use Inertia\Inertia;

class SystemSettingController extends Controller
{
    public function getGeneralSettings(GeneralSettings $settings)
    {
        return Inertia::render('systemSettings/GeneralSetting', 
            GeneralSettingsData::from($settings)
        );
    }

    public function updateGeneralSettings(GeneralSettingsData $data, UpdateSettingAction $action)
    {
        $action->execute($data);

        return back();
    }
}
