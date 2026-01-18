<?php

namespace App\Data\User;

use App\Models\User;
use Spatie\LaravelData\Data;

class ShowEditUserFormData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $avatar,
        public string $email,
        public bool $two_factor_enabled,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: (string) $user->id,
            name: $user->name,
            avatar: filled($user->avatar) ? $user->avatar : null,
            email: $user->email,
            two_factor_enabled: filled($user->two_factor_confirmed_at),
        );
    }
}
