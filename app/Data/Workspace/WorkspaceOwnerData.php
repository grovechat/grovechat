<?php

namespace App\Data\Workspace;

use App\Models\User;
use Spatie\LaravelData\Data;

class WorkspaceOwnerData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }
}

