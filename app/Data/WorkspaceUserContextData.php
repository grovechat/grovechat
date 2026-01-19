<?php

namespace App\Data;

use App\Enums\UserOnlineStatus;
use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Carbon;
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
        public bool $show_remove_button = true,
        public ?string $user_nickname = null,
        public ?string $user_last_active_at = null,
        public ?string $user_avatar = null,
    ) {}

    public static function fromModels(Workspace $workspace, User $user): self
    {
        $roleValue = $user->pivot?->role ?? $workspace->users()->whereKey($user->id)->value('user_workspace.role');
        $role = WorkspaceRole::tryFrom((string) $roleValue) ?? null;

        $pivotNickname = $user->pivot?->nickname ?? $workspace->users()->whereKey($user->id)->value('user_workspace.nickname');
        $pivotOnlineStatus = $user->pivot?->online_status ?? $workspace->users()->whereKey($user->id)->value('user_workspace.online_status') ?? UserOnlineStatus::ONLINE->value;
        $pivotLastActiveAt = $user->pivot?->last_active_at ?? $workspace->users()->whereKey($user->id)->value('user_workspace.last_active_at');
        $onlineStatusEnum = UserOnlineStatus::from((int) $pivotOnlineStatus);
        $lastActiveAt = filled($pivotLastActiveAt) ? Carbon::parse($pivotLastActiveAt)->toIso8601String() : null;

        return new self(
            workspace_id: (string) $workspace->id,
            workspace_slug: $workspace->slug,
            workspace_name: $workspace->name,
            user_id: (string) $user->id,
            user_name: $user->name,
            user_email: $user->email,
            user_avatar: filled($user->avatar) ? $user->avatar : null,
            user_online_status: EnumOptionData::fromEnum($onlineStatusEnum),
            user_nickname: filled($pivotNickname) ? (string) $pivotNickname : null,
            user_last_active_at: $lastActiveAt,
            role: EnumOptionData::fromEnum($role),
        );
    }

    public function withShowRemoveButton(bool $showRemoveButton): self
    {
        $this->show_remove_button = $showRemoveButton;

        return $this;
    }
}
