<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| 这些路由处理所有认证相关的功能，采用 DDD 架构
|
*/

// 登录路由
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticationController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthenticationController::class, 'login']);
});

// 注册路由
if (Features::enabled(Features::registration())) {
    Route::middleware('guest')->group(function () {
        Route::get('/register', [AuthenticationController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthenticationController::class, 'register']);
    });
}

// 登出路由
Route::post('/logout', [AuthenticationController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// 密码重置路由
if (Features::enabled(Features::resetPasswords())) {
    Route::middleware('guest')->group(function () {
        Route::get('/forgot-password', [PasswordController::class, 'showForgotPasswordForm'])
            ->name('password.request');

        Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])
            ->name('password.email');

        Route::get('/reset-password/{token}', [PasswordController::class, 'showResetPasswordForm'])
            ->name('password.reset');

        Route::post('/reset-password', [PasswordController::class, 'resetPassword'])
            ->name('password.update');
    });
}

// 邮箱验证路由
if (Features::enabled(Features::emailVerification())) {
    Route::middleware('auth')->group(function () {
        Route::get('/email/verify', function () {
            return inertia('auth/VerifyEmail', [
                'status' => session('status'),
            ]);
        })->name('verification.notice');

        Route::get('/email/verify/{id}/{hash}', function () {
            // Fortify 会自动处理这个路由
        })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

        Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('status', 'verification-link-sent');
        })->middleware('throttle:6,1')->name('verification.send');
    });
}

// 密码确认路由
Route::middleware('auth')->group(function () {
    Route::get('/confirm-password', function () {
        return inertia('auth/ConfirmPassword');
    })->name('password.confirm');

    // Fortify 会处理 POST 请求
});

// 双因素认证挑战路由
if (Features::enabled(Features::twoFactorAuthentication())) {
    Route::middleware('guest')->group(function () {
        Route::get('/two-factor-challenge', function () {
            return inertia('auth/TwoFactorChallenge');
        })->name('two-factor.login');

        // Fortify 会处理 POST 请求
    });

    // 双因素认证管理 API (全局路由,不需要租户)
    Route::middleware('auth')->prefix('user')->group(function () {
        Route::post('/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])->name('two-factor.enable');
        Route::delete('/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])->name('two-factor.disable');
        Route::post('/confirmed-two-factor-authentication', [TwoFactorAuthenticationController::class, 'confirm'])->name('two-factor.confirm');
        Route::get('/two-factor-qr-code', [TwoFactorAuthenticationController::class, 'qrCode'])->name('two-factor.qr-code');
        Route::get('/two-factor-secret-key', [TwoFactorAuthenticationController::class, 'secretKey'])->name('two-factor.secret-key');
        Route::get('/two-factor-recovery-codes', [TwoFactorAuthenticationController::class, 'recoveryCodes'])->name('two-factor.recovery-codes');
        Route::post('/two-factor-recovery-codes', [TwoFactorAuthenticationController::class, 'generateRecoveryCodes'])->name('two-factor.recovery-codes.generate');
    });
}
