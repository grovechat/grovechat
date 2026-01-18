<?php

namespace App\Data;

use App\Models\User;
use Spatie\LaravelData\Data;

class SystemUserListItemData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $avatar,
        public bool $two_factor_enabled,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: (string) $user->id,
            name: $user->name,
            email: $user->email,
            avatar: filled($user->avatar) ? $user->avatar : null,
            two_factor_enabled: filled($user->two_factor_confirmed_at),
        );
    }
}
