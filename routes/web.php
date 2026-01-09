<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Settings\AppearanceController;
use App\Http\Controllers\Settings\LanguageController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\WorkspaceSettingController;
use App\Http\Middleware\IdentifyWorkspace;
use App\Http\Middleware\TrackLastWorkspace;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', IdentifyWorkspace::class, TrackLastWorkspace::class])->prefix('w/{slug}')->group(function () {
    Route::get('/', [HomeController::class, 'workspaceHome'])->name('workspace.home');
    Route::get('dashboard', [HomeController::class, 'workspaceDashboard'])->name('workspace.dashboard');
    
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
    Route::prefix("system-settings")->group(function () {
        // 基础设置 
        Route::get('general', [SystemSettingController::class, 'getGeneralSettings'])->name('system-setting.get-general-settings');
        Route::put('general', [SystemSettingController::class, 'updateGeneralSettings'])->name('system-setting.update-general-settings');
        
        // 存储设置
        Route::get('storage', function () {
            return Inertia::render('systemSettings/StorageSetting');
        })->name('system-setting.get-storage-settings');

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
        
    // 管理中心--工作区--常规设置
    Route::prefix('workspace-settings/workspaces')->group(function () {
        Route::get('/', [WorkspaceSettingController::class, 'showWorkspaceGeneralPage'])->name('workspace-setting.workspace.general');
        Route::get('/create', [WorkspaceSettingController::class, 'showCreateWorkspacePage'])->name('workspace-settings.workspace.create');
        Route::put('/', [WorkspaceSettingController::class, 'updateWorkspace'])->name('workspace-settings.workspace.update');
        Route::post('/', [WorkspaceSettingController::class, 'storeWorkspace'])->name('workspace-settings.workspace.addWorkspace');
        Route::delete('/', [WorkspaceSettingController::class, 'deleteWorkspace'])->name('workspace-settings.workspace.delete');
    });
    
    // 管理中心--客服--多客服
    Route::prefix('workspace-settings/teammates')->group(function () {
        Route::get('/', function () {
            return Inertia::render('workspaceSettings/teammate/Index');
        })->name('workspace-setting.teammate.index');
    });
    
    // 管理中心--渠道--网站
    Route::prefix('workspace-settings/channels/web')->group(function () {
        Route::get('/', function () {
            return Inertia::render('workspaceSettings/channels/Web');
        })->name('workspace-setting.channels.web');
    }); 
    
    // 管理中心--数据--标签
    Route::prefix('workspace-settings/tags')->group(function () {
        Route::get('/', function () {
            return Inertia::render('workspaceSettings/datas/Tag');
        })->name('workspace-setting.datas.tag');
    });
    
    // 管理中心--数据--自定义属性
    Route::prefix('workspace-settings/attributes')->group(function () {
        Route::get('/', function () {
            return Inertia::render('workspaceSettings/datas/Attribute');
        })->name('workspace-setting.datas.attribute');
    });
    
    // 联系人
    Route::prefix('/contacts')->group(function () {
        Route::get('contacts/{type}/index', function () {
            return Inertia::render('contacts/Index');
        })
        ->whereIn('type', ['all', 'customers', 'leads'])
        ->name('contact.index');
    }); 

    // 管理中心--访客
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
