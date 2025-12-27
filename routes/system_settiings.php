<?php

use App\Http\Controllers\SystemSettings\GeneralSettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/general-settings', [GeneralSettingController::class, 'index'])->name('general-setting.index');
    Route::put('/general-settings', [GeneralSettingController::class, 'update'])->name('general-setting.update');
});
