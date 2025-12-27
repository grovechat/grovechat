<?php

use App\Http\Controllers\SystemSettingController;
use Illuminate\Support\Facades\Route;

/**
 * 常规设置
 */
Route::get('system-settings/general', [SystemSettingController::class, 'getGeneralSettings'])->name('system-setting.get-general-settings');
Route::put('system-settings/general', [SystemSettingController::class, 'updateGeneralSettings'])->name('system-setting.update-general-settings');
