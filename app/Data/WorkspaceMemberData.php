<?php

namespace App\Data;

use App\Models\User;
use Spatie\LaravelData\Data;

class WorkspaceMemberData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $role,
        public ?string $joined_at,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role: $user->pivot?->role,
            joined_at: $user->pivot?->created_at?->toIso8601String(),
        );
    }
}

