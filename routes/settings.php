<?php

use App\Http\Controllers\Auth\TwoFactorAuthenticationController;
use App\Http\Controllers\Settings\AppearanceController;
use App\Http\Controllers\Settings\LanguageController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('settings', 'settings/profile');

// Profile 设置
Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// 密码设置
Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');
Route::put('settings/password', [PasswordController::class, 'update'])->middleware('throttle:6,1')->name('user-password.update');

// 双因素认证设置 (使用新的 DDD 控制器)
Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])->name('two-factor.show');
Route::post('settings/two-factor', [TwoFactorAuthenticationController::class, 'store'])->name('two-factor.enable');
Route::post('settings/two-factor/confirm', [TwoFactorAuthenticationController::class, 'confirm'])->name('two-factor.confirm');
Route::delete('settings/two-factor', [TwoFactorAuthenticationController::class, 'destroy'])->name('two-factor.disable');
Route::get('settings/two-factor/qr-code', [TwoFactorAuthenticationController::class, 'qrCode'])->name('two-factor.qr-code');
Route::get('settings/two-factor/recovery-codes', [TwoFactorAuthenticationController::class, 'recoveryCodes'])->name('two-factor.recovery-codes');
Route::post('settings/two-factor/recovery-codes', [TwoFactorAuthenticationController::class, 'generateRecoveryCodes'])->name('two-factor.recovery-codes.generate');

// 外观和语言设置
Route::get('settings/appearance', [AppearanceController::class, 'edit'])->name('appearance.edit');

Route::get('settings/language', [LanguageController::class, 'edit'])->name('language.edit');
