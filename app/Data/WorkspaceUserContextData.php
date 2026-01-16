<?php

namespace App\Data;

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Spatie\LaravelData\Data;

class WorkspaceUserContextData extends Data
{
    public function __construct(
        public WorkspaceData $workspace,
        public WorkspaceUserData $user,
    ) {}

    public static function fromModels(Workspace $workspace, User $user): self
    {
        $role = WorkspaceRole::tryFrom((string) $workspace->users()->whereKey($user->id)->value('user_workspace.role')) ?? null;

        return new self(
            workspace: new WorkspaceData(
                id: (string) $workspace->id,
                name: $workspace->name,
                slug: $workspace->slug,
                logoUrl: (string) $workspace->logo_url,
                ownerId: filled($workspace->owner_id) ? (string) $workspace->owner_id : null,
                logoId: filled($workspace->logo_id) ? (string) $workspace->logo_id : null,
                role: $role,
            ),
            user: new WorkspaceUserData(
                id: (string) $user->id,
                name: $user->name,
                nickname: filled($user->nickname) ? $user->nickname : null,
                avatar: filled($user->avatar) ? $user->avatar : null,
                email: $user->email,
                role: $role,
            ),
        );
    }
}
