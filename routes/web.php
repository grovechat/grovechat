<?php

use App\Actions\Dashboard\RedirectCurrentWorkspaceDashboard;
use App\Actions\Dashboard\RedirectLastDashboardAction;
use App\Actions\Dashboard\ShowDashboardAction;
use App\Actions\Home\ShowHomePageAction;
use App\Actions\manage\CreateWorkspaceAction;
use App\Actions\Manage\DeleteCurrentWorkspaceAction;
use App\Actions\Manage\GetCurrentWorkspaceAction;
use App\Actions\Manage\ShowCreateWorkspacePageAction;
use App\Actions\Manage\UpdateWorkspaceAction;
use App\Actions\StorageSetting\CheckStorageSettingAction;
use App\Actions\StorageSetting\GetStorageSettingAction;
use App\Actions\StorageSetting\UpdateStorageSettingAction;
use App\Actions\StorageSetting\StorageProfile\CheckStorageProfileAction;
use App\Actions\StorageSetting\StorageProfile\CreateStorageProfileAction;
use App\Actions\StorageSetting\StorageProfile\DeleteStorageProfileAction;
use App\Actions\StorageSetting\StorageProfile\UpdateStorageProfileAction;
use App\Actions\SystemSetting\GetGeneralSettingAction;
use App\Actions\SystemSetting\UpdateGeneralSettingAction;
use App\Http\Controllers\Settings\AppearanceController;
use App\Http\Controllers\Settings\LanguageController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Http\Middleware\IdentifyWorkspace;
use App\Http\Middleware\TrackLastWorkspace;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', ShowHomePageAction::class)->name('home');
Route::get('/dashboard', RedirectLastDashboardAction::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', IdentifyWorkspace::class, TrackLastWorkspace::class])->prefix('w/{slug}')->group(function () {
    Route::get('/', RedirectCurrentWorkspaceDashboard::class)->name('workspace.home');
    Route::get('/dashboard', ShowDashboardAction::class)->name('workspace.dashboard');

    // 个人设置
    Route::prefix('settings')->group(function () {
        // 个人资料
        Route::redirect('/', 'settings/profile');
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // 密码
        Route::get('password', [PasswordController::class, 'edit'])->name('user-password.edit');
        Route::put('password', [PasswordController::class, 'update'])->middleware('throttle:6,1')->name('user-password.update');

        // 两步认证
        Route::get('two-factor', [TwoFactorAuthenticationController::class, 'show'])->name('two-factor.show');

        // 语言和时区
        Route::get('language', [LanguageController::class, 'edit'])->name('language.edit');

        // 外观
        Route::get('appearance', [AppearanceController::class, 'edit'])->name('appearance.edit');
    });

    // 系统设置
    Route::prefix('system-settings')->group(function () {
        // 基础设置
        Route::get('general', GetGeneralSettingAction::class)->name('get-general-setting');
        Route::put('general', UpdateGeneralSettingAction::class)->name('update-general-setting');

        // 存储设置
        Route::get('storage', GetStorageSettingAction::class)->name('get-storage-setting');
        Route::put('storage', UpdateStorageSettingAction::class)->name('update-storage-setting');
        Route::put('check', CheckStorageSettingAction::class)->name('check-storage-settiing');
        Route::post('storage/profiles', CreateStorageProfileAction::class)->name('storage-profile.create');
        Route::put('storage/profiles/{profile}', UpdateStorageProfileAction::class)->name('storage-profile.update');
        Route::put('storage/profiles/{profile}/check', CheckStorageProfileAction::class)->name('storage-profile.check');
        Route::delete('storage/profiles/{profile}', DeleteStorageProfileAction::class)->name('storage-profile.delete');

        // 邮箱服务器
        Route::get('mail', function () {
            return Inertia::render('systemSettings/MailSetting');
        })->name('system-setting.get-mail-settings');

        // 外部集成
        Route::get('integration', function () {
            return Inertia::render('systemSettings/IntegrationSetting');
        })->name('system-setting.get-integration-settings');

        // 安全
        Route::get('security', function () {
            return Inertia::render('systemSettings/SecuritySetting');
        })->name('system-setting.get-security-settings');

        // 维护
        Route::get('/maintenance', function () {
            return Inertia::render('systemSettings/MaintenanceSetting');
        })->name('system-setting.get-maintenance-settings');
    });

    // 管理中心
    Route::prefix('manage')->group(function () {
        // 工作区
        Route::get('workspaces/current', GetCurrentWorkspaceAction::class)->name('get-current-workspace');  
        Route::get('workspaces/create', ShowCreateWorkspacePageAction::class)->name('show-create-workspace-page');
        Route::put('workspaces', UpdateWorkspaceAction::class)->name('update-current-workspace');
        Route::post('workspaces', CreateWorkspaceAction::class)->name('create-workspace');
        Route::delete('workspaces/current', DeleteCurrentWorkspaceAction::class)->name('delete-current-workspace');

        // 客服
        Route::prefix('teammates')->group(function () {
            Route::get('/', function () {
                return Inertia::render('workspaceSettings/teammate/Index');
            })->name('workspace-setting.teammate.index');
        });

        // 渠道
        Route::prefix('channels')->group(function () {
            // 网站
            Route::prefix('web')->group(function () {
                Route::get('/', function () {
                    return Inertia::render('workspaceSettings/channels/Web');
                })->name('workspace-setting.channels.web');
            });
        });

        // 标签
        Route::prefix('tags')->group(function () {
            Route::get('/', function () {
                return Inertia::render('workspaceSettings/datas/Tag');
            })->name('workspace-setting.datas.tag');
        });

        // 自定义属性
        Route::prefix('attributes')->group(function () {
            Route::get('/', function () {
                return Inertia::render('workspaceSettings/datas/Attribute');
            })->name('workspace-setting.datas.attribute');
        });
    });

    // 联系人
    Route::prefix('contacts')->group(function () {
        Route::get('/{type}/index', function () {
            return Inertia::render('contacts/Index');
        })
            ->whereIn('type', ['all', 'customers', 'leads'])
            ->name('contact.index');
    });

    // 会话
    Route::prefix('/conversations')->group(function () {
        Route::get('/', function () {
            return Inertia::render('contacts/Conversation');
        })->name('contact.conversations');
    });

    // 统计
    Route::get('/stats', function () {
        return Inertia::render('stats/Index');
    })->name('stats.index');
});
