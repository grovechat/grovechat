<?php

namespace App\Data\Teammate;

use App\Enums\UserOnlineStatus;
use App\Enums\WorkspaceRole;
use App\Models\User;
use Spatie\LaravelData\Data;

class ListTeammateItemData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $avatar,
        public string $email,
        public WorkspaceRole $role,
        public string $role_label,
        public UserOnlineStatus $online_status,
        public string $online_status_label,
        public bool $can_delete = false,
    ) {}

    public static function fromModel(User $user, bool $canDelete = false): self
    {
        $role = WorkspaceRole::tryFrom((string) ($user->pivot?->role ?? '')) ?? WorkspaceRole::OPERATOR;
        $status = UserOnlineStatus::tryFrom((int) $user->online_status) ?? UserOnlineStatus::OFFLINE;

        return new self(
            id: (string) $user->id,
            name: $user->name,
            avatar: filled($user->avatar) ? $user->avatar : null,
            email: $user->email,
            role: $role,
            role_label: $role->label(),
            online_status: $status,
            online_status_label: $status->label(),
            can_delete: $canDelete,
        );
    }
}
