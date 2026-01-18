<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;

class SystemUserCreateFormData extends Data
{
    public function __construct(
        public string $name = '',
        public ?string $avatar = null,
        public string $email = '',
    ) {}
}
