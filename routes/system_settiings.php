<?php

use App\Http\Controllers\SystemSettingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/**
 * 基础设置
 */
Route::get('system-settings/general', [SystemSettingController::class, 'getGeneralSettings'])->name('system-setting.get-general-settings');
Route::put('system-settings/general', [SystemSettingController::class, 'updateGeneralSettings'])->name('system-setting.update-general-settings');

/**
 * 存储设置
 */
Route::get('system-settings/storage', function () {
    return Inertia::render('systemSettings/StorageSetting');
})->name('system-setting.get-storage-settings');

/**
 * 邮箱服务器
 */
Route::get('system-settings/mail', function () {
    return Inertia::render('systemSettings/MailSetting');
})->name('system-setting.get-mail-settings');

/**
 * 集成
 */
Route::get('system-settings/integration', function () {
    return Inertia::render('systemSettings/IntegrationSetting');
})->name('system-setting.get-integration-settings');

/**
 * 安全
 */
Route::get('system-settings/security', function () {
    return Inertia::render('systemSettings/SecuritySetting');
})->name('system-setting.get-security-settings');

/**
 * 维护
 */
Route::get('system-settings/maintenance', function () {
    return Inertia::render('systemSettings/MaintenanceSetting');
})->name('system-setting.get-maintenance-settings');
