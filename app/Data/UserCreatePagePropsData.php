<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class UserCreatePagePropsData extends Data
{
    public function __construct(
        public UserCreateFormData $user_form,
        /** @var \App\Data\EnumOptionData[] */
        public array $role_options,
    ) {}
}
