<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;

class SystemUserCreatePagePropsData extends Data
{
    public function __construct(
        public SystemUserCreateFormData $user_form,
    ) {}
}
