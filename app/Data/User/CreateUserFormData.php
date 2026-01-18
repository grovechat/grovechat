<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;

class CreateUserFormData extends Data
{
    public function __construct(
        public string $name = '',
        public ?string $avatar = null,
        public string $email = '',
    ) {}
}
