<?php

/**
 * 系统设置
 */

namespace App\Http\Controllers;

use App\Domain\SystemSettings\Actions\GetSettingAction;
use App\Domain\SystemSettings\Actions\UpdateSettingAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemSettingController extends Controller
{
    public function getGeneralSettings()
    {
        $action = app(GetSettingAction::class); 
        return Inertia::render('systemSettings/GeneralSetting', $action->execute());
    }

    public function updateGeneralSettings(Request $request, UpdateSettingAction $action)
    {
        try {
            $action->execute($request->all());
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
        return back();
    }
}
