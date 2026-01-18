<?php

namespace App\Data\Teammate;

use App\Enums\WorkspaceRole;
use App\Models\User;
use Spatie\LaravelData\Data;

class TrashTeammateData extends Data
{
    public function __construct(
        public string $id,
        public ?string $avatar,
        public string $name,
        public string $email,
        public WorkspaceRole $role,
        public ?string $deleted_at,
    ) {}

    public static function fromModel(User $user): self
    {
        $role = WorkspaceRole::tryFrom((string) ($user->pivot?->role ?? '')) ?? WorkspaceRole::OPERATOR;

        return new self(
            id: (string) $user->id,
            avatar: filled($user->avatar) ? $user->avatar : null,
            name: $user->name,
            email: $user->email,
            role: $role,
            deleted_at: $user->deleted_at?->toIso8601String(),
        );
    }
}
