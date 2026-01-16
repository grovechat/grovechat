<?php

namespace App\Data;

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Spatie\LaravelData\Data;

class WorkspaceUserContextData extends Data
{
    public function __construct(
        public string $workspace_id,
        public string $workspace_slug,
        public string $workspace_name,
        public string $user_id,
        public string $user_name,
        public string $user_email,
        public ?string $user_nickname = null,
        public ?string $user_avatar = null,
        public ?WorkspaceRole $role = null,
    ) {}

    public static function fromModels(Workspace $workspace, User $user): self
    {
        $role = WorkspaceRole::tryFrom((string) $workspace->users()->whereKey($user->id)->value('user_workspace.role')) ?? null;

        return new self(
            workspace_id: (string) $workspace->id,
            workspace_slug: $workspace->slug,
            workspace_name: $workspace->name,
            user_id: (string) $user->id,
            user_name: $user->name,
            user_email: $user->email,
            user_nickname: filled($user->nickname) ? $user->nickname : null,
            user_avatar: filled($user->avatar) ? $user->avatar : null,
            role: $role,
        );
    }
}
