<?php

use App\Actions\Dashboard\RedirectCurrentWorkspaceDashboard;
use App\Actions\Dashboard\RedirectLastDashboardAction;
use App\Actions\Dashboard\ShowDashboardAction;
use App\Actions\Home\ShowHomePageAction;
use App\Actions\Manage\CreateWorkspaceAction;
use App\Actions\Manage\DeleteCurrentWorkspaceAction;
use App\Actions\Manage\GetCurrentWorkspaceAction;
use App\Actions\Manage\ShowCreateWorkspacePageAction;
use App\Actions\Manage\UpdateWorkspaceAction;
use App\Actions\Security\LogoutAdminAction;
use App\Actions\Security\LogoutWebAction;
use App\Actions\StorageSetting\CheckStorageSettingAction;
use App\Actions\StorageSetting\GetStorageSettingAction;
use App\Actions\StorageSetting\StorageProfile\CheckStorageProfileAction;
use App\Actions\StorageSetting\StorageProfile\CreateStorageProfileAction;
use App\Actions\StorageSetting\StorageProfile\DeleteStorageProfileAction;
use App\Actions\StorageSetting\StorageProfile\UpdateStorageProfileAction;
use App\Actions\StorageSetting\UpdateStorageSettingAction;
use App\Actions\SystemSetting\GetGeneralSettingAction;
use App\Actions\SystemSetting\UpdateGeneralSettingAction;
use App\Actions\SystemSetting\User\CreateSystemUserAction;
use App\Actions\SystemSetting\User\ShowCreateSystemUserPageAction;
use App\Actions\SystemSetting\User\ShowEditSystemUserPageAction;
use App\Actions\SystemSetting\User\ShowSystemUserListAction;
use App\Actions\SystemSetting\User\UpdateSystemUserAction;
use App\Actions\User\CreateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\RestoreUserAction;
use App\Actions\User\ShowCreateUserPageAction;
use App\Actions\User\ShowEditUserPageAction;
use App\Actions\User\ShowUserListAction;
use App\Actions\User\ShowUserTrashAction;
use App\Actions\User\UpdateUserAction;
use App\Actions\User\UpdateUserOnlineStatusAction;
use App\Actions\Workspace\DeleteWorkspaceAction;
use App\Actions\Workspace\GetWorkspaceListAction;
use App\Actions\Workspace\GetWorkspaceTrashListAction;
use App\Actions\Workspace\LoginAsWorkspaceOwnerAction;
use App\Actions\Workspace\RestoreWorkspaceAction;
use App\Actions\Workspace\ShowWorkspaceDetailAction;
use App\Http\Controllers\Settings\AppearanceController;
use App\Http\Controllers\Settings\LanguageController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Http\Middleware\AuthenticateSettings;
use App\Http\Middleware\CheckSuperAdmin;
use App\Http\Middleware\IdentifyWorkspace;
use App\Http\Middleware\TrackLastWorkspace;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', ShowHomePageAction::class)->name('home');
Route::get('/dashboard', RedirectLastDashboardAction::class)->middleware(['auth:web'])->name('dashboard');

// 个人设置（全局，不绑定工作区）
Route::middleware([AuthenticateSettings::class, IdentifyWorkspace::class, TrackLastWorkspace::class])->prefix('settings')->group(function () {
    Route::redirect('/', '/settings/profile');

    // 个人资料
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

// 系统设置（仅超级管理员）
Route::prefix('admin')->middleware(['auth:admin', CheckSuperAdmin::class])->group(function () {
    Route::redirect('/', '/admin/general')->name('admin.home');

    // 基础设置
    Route::get('general', GetGeneralSettingAction::class)->name('admin.get-general-setting');
    Route::put('general', UpdateGeneralSettingAction::class)->name('admin.update-general-setting');

    // 工作区管理
    Route::get('workspaces', GetWorkspaceListAction::class)->name('admin.get-workspace-list');
    Route::get('workspaces/trash', GetWorkspaceTrashListAction::class)->name('admin.get-workspace-trash');
    Route::get('workspaces/{id}', ShowWorkspaceDetailAction::class)->name('admin.show-workspace-detail');
    Route::delete('workspaces/{id}', DeleteWorkspaceAction::class)->name('admin.delete-workspace');
    Route::put('workspaces/{id}/restore', RestoreWorkspaceAction::class)->name('admin.restore-workspace');
    Route::get('workspaces/{id}/login-as-owner', LoginAsWorkspaceOwnerAction::class)->name('admin.login-as-workspace-owner');

    // 存储设置
    Route::get('storage', GetStorageSettingAction::class)->name('admin.get-storage-setting');
    Route::put('storage', UpdateStorageSettingAction::class)->name('admin.update-storage-setting');
    Route::put('check', CheckStorageSettingAction::class)->name('admin.check-storage-settiing');
    Route::post('storage/profiles', CreateStorageProfileAction::class)->name('admin.storage-profile.create');
    Route::put('storage/profiles/{profile}', UpdateStorageProfileAction::class)->name('admin.storage-profile.update');
    Route::put('storage/profiles/{profile}/check', CheckStorageProfileAction::class)->name('admin.storage-profile.check');
    Route::delete('storage/profiles/{profile}', DeleteStorageProfileAction::class)->name('admin.storage-profile.delete');

    // 用户管理
    Route::get('users', ShowSystemUserListAction::class)->name('admin.get-user-list');
    Route::get('users/create', ShowCreateSystemUserPageAction::class)->name('admin.show-create-user-page');
    Route::post('users', CreateSystemUserAction::class)->name('admin.create-user');
    Route::get('users/{id}/edit', ShowEditSystemUserPageAction::class)->name('admin.show-edit-user-page');
    Route::put('users/{id}', UpdateSystemUserAction::class)->name('admin.update-user');

    // 邮箱服务器
    Route::get('mail', function () {
        return Inertia::render('admin/systemSettings/MailSetting');
    })->name('admin.get-mail-settings');

    // 外部集成
    Route::get('integration', function () {
        return Inertia::render('admin/systemSettings/IntegrationSetting');
    })->name('admin.get-integration-settings');

    // 安全
    Route::get('security', function () {
        return Inertia::render('admin/systemSettings/SecuritySetting');
    })->name('admin.get-security-settings');

    // 维护
    Route::get('maintenance', function () {
        return Inertia::render('admin/systemSettings/MaintenanceSetting');
    })->name('admin.get-maintenance-settings');
});

// 分 guard 登出（保证同一浏览器同时操作 admin + workspace 时互不影响）
Route::post('/logout/admin', LogoutAdminAction::class)->middleware(['auth:admin'])->name('logout.admin');
Route::post('/logout/web', LogoutWebAction::class)->middleware(['auth:web'])->name('logout.web');

Route::middleware(['auth:web', IdentifyWorkspace::class, TrackLastWorkspace::class])->prefix('w/{slug}')->group(function () {
    Route::get('/', RedirectCurrentWorkspaceDashboard::class)->name('workspace.home');
    Route::get('/dashboard', ShowDashboardAction::class)->name('workspace.dashboard');

    // 管理中心
    Route::prefix('manage')->group(function () {
        // 工作区
        Route::get('workspaces/current', GetCurrentWorkspaceAction::class)->name('get-current-workspace');
        Route::get('workspaces/create', ShowCreateWorkspacePageAction::class)->name('show-create-workspace-page');
        Route::put('workspaces/current', UpdateWorkspaceAction::class)->name('update-current-workspace');
        Route::post('workspaces', CreateWorkspaceAction::class)->name('create-workspace');
        Route::delete('workspaces/current', DeleteCurrentWorkspaceAction::class)->name('delete-current-workspace');

        // 多客服
        Route::get('users', ShowUserListAction::class)->name('show-user-list');
        Route::get('users/create', ShowCreateUserPageAction::class)->name('show-create-user-page');
        Route::get('users/{id}/edit', ShowEditUserPageAction::class)->name('show-edit-user-page');
        Route::get('users/trash', ShowUserTrashAction::class)->name('show-user-trash-page');
        Route::post('users', CreateUserAction::class)->name('create-user');
        Route::put('users/{id}', UpdateUserAction::class)->name('update-user');
        Route::put('users/{id}/online-status', UpdateUserOnlineStatusAction::class)->name('update-user-online-status');
        Route::put('users/{id}/restore', RestoreUserAction::class)->name('restore-user');
        Route::delete('users/{id}', DeleteUserAction::class)->name('delete-user');

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
