<?php

/**
 * 系统设置
 */

namespace App\Http\Controllers;

use App\Domain\SystemSettings\Services\GeneralSettingService;
use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemSettingController extends Controller
{
    public function __construct(
        private GeneralSettingService $settingService
    ) {}

    public function getGeneralSettings(GeneralSettings $settings)
    {
        return Inertia::render('systemSettings/GeneralSetting', $settings);
    }

    public function updateGeneralSettings(Request $request)
    {
        $validated = $request->validate([
            'baseUrl' => 'required|string|max:255|url',
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'copyright' => 'nullable|string|max:255',
            'icpRecord' => 'nullable|string|max:255',
        ]);

        $this->settingService->updateSettings($validated);

        return back();
    }
}
