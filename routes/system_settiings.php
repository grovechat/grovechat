<?php

use App\Http\Controllers\SystemSettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('system-settings')->group(function () {
    Route::get('/general', [SystemSettingController::class, 'getGeneralSettings'])
        ->name('system-setting.get-general-settings');
    Route::put('/general', [SystemSettingController::class, 'updateGeneralSettings'])
        ->name('system-setting.update-general-settings');
});
