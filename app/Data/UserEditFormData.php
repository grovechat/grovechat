<?php

namespace App\Data;

use App\Enums\UserOnlineStatus;
use App\Enums\WorkspaceRole;
use App\Models\User;
use Spatie\LaravelData\Data;

class UserEditFormData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $external_nickname,
        public ?string $avatar,
        public string $email,
        public WorkspaceRole $role,
        public UserOnlineStatus $online_status,
    ) {}

    public static function fromModel(User $user): self
    {
        $role = WorkspaceRole::tryFrom((string) ($user->pivot?->role ?? '')) ?? WorkspaceRole::CUSTOMER_SERVICE;
        $status = UserOnlineStatus::tryFrom((int) $user->online_status) ?? UserOnlineStatus::OFFLINE;

        return new self(
            id: (string) $user->id,
            name: $user->name,
            external_nickname: filled($user->external_nickname) ? $user->external_nickname : null,
            avatar: filled($user->avatar) ? $user->avatar : null,
            email: $user->email,
            role: $role,
            online_status: $status,
        );
    }
}
