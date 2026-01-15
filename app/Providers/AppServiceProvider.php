<?php

namespace App\Providers;

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
        Gate::define('workspace-users.canUpdateRole', function (User $actor, Workspace $workspace, User $target): bool {
            $roles = $workspace->users()
                ->whereIn('users.id', [(string) $actor->id, (string) $target->id])
                ->pluck('user_workspace.role', 'users.id');

            $actorRole = WorkspaceRole::tryFrom((string) ($roles[(string) $actor->id] ?? ''));
            $targetRole = WorkspaceRole::tryFrom((string) ($roles[(string) $target->id] ?? ''));

            return $actorRole === WorkspaceRole::OWNER
                && (string) $actor->id !== (string) $target->id
                && $targetRole !== WorkspaceRole::OWNER;
        });

        Gate::define('workspace-users.updateRole', function (User $actor, Workspace $workspace, User $target, WorkspaceRole $newRole): bool {
            $roles = $workspace->users()
                ->whereIn('users.id', [(string) $actor->id, (string) $target->id])
                ->pluck('user_workspace.role', 'users.id');

            $actorRole = WorkspaceRole::tryFrom((string) ($roles[(string) $actor->id] ?? ''));
            $targetRole = WorkspaceRole::tryFrom((string) ($roles[(string) $target->id] ?? ''));

            return $actorRole === WorkspaceRole::OWNER
                && (string) $actor->id !== (string) $target->id
                && $targetRole !== WorkspaceRole::OWNER
                && in_array($newRole, WorkspaceRole::assignableCases(), true);
        });

        Gate::define('workspace-users.updatePassword', function (User $actor, Workspace $workspace): bool {
            $actorRole = WorkspaceRole::tryFrom((string) $workspace->users()->whereKey($actor->id)->value('user_workspace.role'));

            return $actorRole === WorkspaceRole::OWNER;
        });
    }
}
