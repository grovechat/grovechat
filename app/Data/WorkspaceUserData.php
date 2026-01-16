<?php

namespace App\Data;

use App\Enums\WorkspaceRole;
use App\Models\User;
use Spatie\LaravelData\Data;

class WorkspaceUserData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $nickname,
        public ?string $avatar,
        public string $email,
        public ?WorkspaceRole $role = null,
    ) {}

    public static function fromWorkspaceUser(User $user): self
    {
        return new self(
            id: (string) $user->id,
            name: $user->name,
            nickname: filled($user->nickname) ? $user->nickname : null,
            avatar: filled($user->avatar) ? $user->avatar : null,
            email: $user->email,
            role: WorkspaceRole::tryFrom((string) ($user->pivot?->role ?? '')) ?? null,
        );
    }
}
