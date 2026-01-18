<?php

namespace App\Data\Teammate;

use App\Enums\UserOnlineStatus;
use App\Enums\WorkspaceRole;
use App\Models\User;
use Spatie\LaravelData\Data;

class TeammateData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $nickname,
        public ?string $avatar,
        public string $email,
        public WorkspaceRole $role,
        public string $role_label,
        public UserOnlineStatus $online_status,
    ) {}

    public static function fromModel(User $user): self
    {
        $role = WorkspaceRole::tryFrom((string) ($user->pivot?->role ?? '')) ?? WorkspaceRole::OPERATOR;
        $onlineStatus = UserOnlineStatus::from((int) ($user->pivot?->online_status ?? UserOnlineStatus::ONLINE->value));

        return new self(
            id: (string) $user->id,
            name: $user->name,
            nickname: filled($user->pivot?->nickname) ? (string) $user->pivot->nickname : null,
            avatar: filled($user->avatar) ? $user->avatar : null,
            email: $user->email,
            role: $role,
            role_label: $role->label(),
            online_status: $onlineStatus,
        );
    }
}
