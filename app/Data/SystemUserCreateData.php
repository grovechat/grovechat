<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SystemUserCreateData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $avatar = null,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
