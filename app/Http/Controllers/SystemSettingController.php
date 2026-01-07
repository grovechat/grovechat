<?php

/**
 * 系统设置
 */

namespace App\Http\Controllers;

use App\Domain\SystemSettings\Actions\GetSettingAction;
use App\Domain\SystemSettings\Actions\UpdateSettingAction;
use App\Domain\SystemSettings\DTOs\GeneralSettingsData;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class SystemSettingController extends Controller
{
    public function getGeneralSettings(GetSettingAction $action)
    {
        return Inertia::render('systemSettings/GeneralSetting', $action->execute());
    }

    public function updateGeneralSettings(GeneralSettingsData $data, UpdateSettingAction $action)
    {
        $action->execute($data);
        
        return back();
    }
}
