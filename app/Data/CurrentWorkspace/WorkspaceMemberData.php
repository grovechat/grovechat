<?php

namespace App\Data\CurrentWorkspace;

use App\Data\EnumOptionData;
use App\Enums\WorkspaceRole;
use App\Models\User;
use Spatie\LaravelData\Data;

class WorkspaceMemberData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?EnumOptionData $role,
        public ?string $joined_at,
        public ?string $deleted_at,
    ) {}

    public static function fromModel(User $user): self
    {
        $role = WorkspaceRole::tryFrom((string) ($user->pivot?->role ?? ''));

        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role: $role ? EnumOptionData::fromEnum($role) : null,
            joined_at: $user->pivot?->created_at?->toIso8601String(),
            deleted_at: $user->deleted_at?->toIso8601String(),
        );
    }
}
