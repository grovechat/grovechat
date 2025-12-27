<?php

namespace App\Http\Controllers\SystemSettings;

use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GeneralSettingController extends Controller
{
    public function index(GeneralSettings $settings)
    {
        return Inertia::render('systemSettings/GeneralSettings', $settings);
    }

    public function update(Request $request, GeneralSettings $settings)
    {
        $validated = $request->validate([
            'baseUrl' => 'required|string|max:255|url',
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'copyright' => 'nullable|string|max:255',
            'icpRecord' => 'nullable|string|max:255',
        ]);

        $settings->baseUrl = $validated['baseUrl'];
        $settings->name = $validated['name'];
        $settings->logo = $validated['logo'];
        $settings->copyright = $validated['copyright'];
        $settings->icpRecord = $validated['icpRecord'];
        $settings->save();

        return back();
    }
}
