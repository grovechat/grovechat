<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Settings\AppearanceController;
use App\Http\Controllers\Settings\LanguageController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\TenantSettingController;
use App\Http\Middleware\IdentifyTenant;
use App\Http\Middleware\TrackLastTenant;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', IdentifyTenant::class, TrackLastTenant::class])
    ->prefix('w/{tenant_path}')
    ->group(function () {
        Route::get('/', [HomeController::class, 'tenantHome'])->name('tenant.home');
        Route::get('dashboard', [HomeController::class, 'tenantDashboard'])->name('tenant.dashboard');
       
        // 个人资料
        Route::redirect('settings', 'settings/profile');
        Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        // 密码
        Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');
        Route::put('settings/password', [PasswordController::class, 'update'])->middleware('throttle:6,1')->name('user-password.update');
        // 两步认证
        Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])->name('two-factor.show');
        // 语言和时区
        Route::get('settings/language', [LanguageController::class, 'edit'])->name('language.edit');
        // 外观
        Route::get('settings/appearance', [AppearanceController::class, 'edit'])->name('appearance.edit');

        // 基础设置
        Route::get('system-settings/general', [
            SystemSettingController::class, 'getGeneralSettings'
        ])->name('system-setting.get-general-settings');
        Route::put('system-settings/general', [
            SystemSettingController::class, 'updateGeneralSettings'
        ])->name('system-setting.update-general-settings');

        // 存储设置
        Route::get('system-settings/storage', function () {
            return Inertia::render('systemSettings/StorageSetting');
        })->name('system-setting.get-storage-settings');

        // 邮箱服务器
        Route::get('system-settings/mail', function () {
            return Inertia::render('systemSettings/MailSetting');
        })->name('system-setting.get-mail-settings');

        // 外部集成
        Route::get('system-settings/integration', function () {
            return Inertia::render('systemSettings/IntegrationSetting');
        })->name('system-setting.get-integration-settings');

        // 安全
        Route::get('system-settings/security', function () {
            return Inertia::render('systemSettings/SecuritySetting');
        })->name('system-setting.get-security-settings');

        // 维护
        Route::get('system-settings/maintenance', function () {
            return Inertia::render('systemSettings/MaintenanceSetting');
        })->name('system-setting.get-maintenance-settings');
        
                
        // 工作区--常规设置
        Route::get('tenant-settings/tenant/general', [
            TenantSettingController::class, 'showTenantGeneralPage'
        ])->name('tenant-setting.tenant.general');
        
        Route::get('tenant-settings/tenant/create', [
            TenantSettingController::class, 'showCreateTenantPage',
        ])->name('tenant-settings.tenant.create');
        
        Route::put('tenant-settings/tenant/updateTenant', [
            TenantSettingController::class, 'updateTenent',
        ])->name('tenant-settings.tenent.update');
        
        Route::post('tenant-settings/tenant/store', [
            TenantSettingController::class, 'storeTenant',
        ])->name('tenant-settings.tenant.addTenant');
        
        Route::delete('tenant-settings/tenant/deleteTenant', [
            TenantSettingController::class, 'deleteTenant',
        ])->name('tenant-settings.tenant.delete');

        // 客服--多客服
        Route::get('tenant-settings/teammate/index', function () {
            return Inertia::render('tenantSettings/teammate/Index');
        })->name('tenant-setting.teammate.index');

        // 渠道--网站
        Route::get('tenant-settings/channels/web', function () {
            return Inertia::render('tenantSettings/channels/Web');
        })->name('tenant-setting.channels.web');

        // 数据--标签
        Route::get('tenant-settings/datas/tag', function () {
            return Inertia::render('tenantSettings/datas/Tag');
        })->name('tenant-setting.datas.tag');

        // 数据--自定义属性
        Route::get('tenant-settings/datas/attribute', function () {
            return Inertia::render('tenantSettings/datas/Attribute');
        })->name('tenant-setting.datas.attribute');

        // 联系人
        Route::get('contacts/{type}/index', function () {
            return Inertia::render('contacts/Index');
        })
        ->whereIn('type', ['all', 'customers', 'leads'])
        ->name('contact.index');

        // 访客
        Route::get('contacts/conversations', function () {
            return Inertia::render('contacts/Conversation');
        })->name('contact.conversations');
        
        // 统计
        Route::get('/stats/overview', function () {
            return Inertia::render('stats/Index');
        })->name('stats.index');
    });
