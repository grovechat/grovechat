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
        public EnumOptionData $user_online_status,
        public EnumOptionData $role,
        public bool $show_delete_button = true,
        public ?string $user_nickname = null,
        public ?string $user_avatar = null,
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
            user_online_status: EnumOptionData::fromEnum($user->online_status),
            user_nickname: filled($user->nickname) ? $user->nickname : null,
            user_avatar: filled($user->avatar) ? $user->avatar : null,
            role: EnumOptionData::fromEnum($role),
        );
    }    
    
    public function withShowDeleteButton(bool $showDeleteButton): self
    {
        $this->show_delete_button = $showDeleteButton;
        return $this;
    }
}
