<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class UserEditPagePropsData extends Data
{
    public function __construct(
        public UserEditFormData $user_form,
        /** @var \App\Data\EnumOptionData[] */
        public array $role_options,
        public bool $can_update_role = false,
        public bool $can_update_password = false,
    ) {}
}
