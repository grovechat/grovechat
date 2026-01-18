<?php

namespace App\Providers;

use App\Data\WorkspaceUserContextData;
use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $actorContext = static function (Workspace $workspace, User $actor): WorkspaceUserContextData {
            if (app()->bound(WorkspaceUserContextData::class)) {
                /** @var WorkspaceUserContextData $ctx */
                $ctx = app(WorkspaceUserContextData::class);
                if ($ctx->workspace_id === (string) $workspace->id && $ctx->user_id === (string) $actor->id) {
                    return $ctx;
                }
            }

            return WorkspaceUserContextData::fromModels($workspace, $actor);
        };

        // 管理中心权限
        Gate::define('workspace.canAccessManageCenter', function (User $actor, Workspace $workspace) use ($actorContext): bool {
            $ctx = $actorContext($workspace, $actor);
            $actorRole = $ctx->role->value;

            return in_array($actorRole, [WorkspaceRole::OWNER->value, WorkspaceRole::ADMIN->value], true);
        });

        // 从工作区移除成员权限（仅解除关联，不删除用户）
        Gate::define('workspace-users.removeMember', function (User $actor, Workspace $workspace, User $target) use ($actorContext): bool {
            $ctx = $actorContext($workspace, $actor);
            $actorRole = $ctx->role->value;
            $targetRole = WorkspaceUserContextData::fromModels($workspace, $target)->role->value;

            if (filled($workspace->owner_id) && (string) $workspace->owner_id === (string) $target->id) {
                return false;
            }

            if ((string) $actor->id === (string) $target->id) {
                return false;
            }

            if ($actorRole === WorkspaceRole::OWNER->value) {
                return true;
            }

            if ($actorRole !== WorkspaceRole::ADMIN->value) {
                return false;
            }

            return $targetRole === WorkspaceRole::OPERATOR->value;
        });

        // 更新用户资料权限
        Gate::define('workspace-users.updateProfile', function (User $actor, Workspace $workspace, User $target) use ($actorContext): bool {
            $ctx = $actorContext($workspace, $actor);
            $actorRole = $ctx->role->value;
            $targetRole = WorkspaceUserContextData::fromModels($workspace, $target)->role->value;

            if ($actorRole === WorkspaceRole::OWNER->value) {
                return true;
            }

            if ($actorRole !== WorkspaceRole::ADMIN->value) {
                return false;
            }

            return (string) $actor->id === (string) $target->id
                || $targetRole === WorkspaceRole::OPERATOR->value;
        });

        Gate::define('workspace-users.updateEmail', function (User $actor, Workspace $workspace, User $target) use ($actorContext): bool {
            $ctx = $actorContext($workspace, $actor);
            $actorRole = $ctx->role->value;

            return $actorRole === WorkspaceRole::OWNER->value
                && (string) $actor->id !== (string) $target->id;
        });

        Gate::define('workspace-users.canUpdateRole', function (User $actor, Workspace $workspace, User $target) use ($actorContext): bool {
            $ctx = $actorContext($workspace, $actor);
            $actorRole = $ctx->role->value;

            return $actorRole === WorkspaceRole::OWNER->value
                && (string) $actor->id !== (string) $target->id;
        });

        Gate::define('workspace-users.updateRole', function (User $actor, Workspace $workspace, User $target, WorkspaceRole $newRole) use ($actorContext): bool {
            $ctx = $actorContext($workspace, $actor);
            $actorRole = $ctx->role->value;

            return $actorRole === WorkspaceRole::OWNER->value
                && (string) $actor->id !== (string) $target->id
                && in_array($newRole, WorkspaceRole::assignableCases(), true);
        });

        Gate::define('workspace-users.updatePassword', function (User $actor, Workspace $workspace) use ($actorContext): bool {
            $ctx = $actorContext($workspace, $actor);
            $actorRole = $ctx->role->value;

            return $actorRole === WorkspaceRole::OWNER->value;
        });
    }
}
