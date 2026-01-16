<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class UserEditPagePropsData extends Data
{
    public function __construct(
        public UserEditFormData $user_form,
        /** @var \App\Data\EnumOptionData[] */
        public array $role_options,
    ) {}
}
