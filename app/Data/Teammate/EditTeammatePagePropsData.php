<?php

namespace App\Data\Teammate;

use Spatie\LaravelData\Data;

class EditTeammatePagePropsData extends Data
{
    public function __construct(
        public EditTeammateFormData $user_form,
        /** @var \App\Data\EnumOptionData[] */
        public array $role_options,
        public bool $can_update_profile,
        public bool $can_update_email,
        public bool $can_update_password,
        public bool $can_update_role,
    ) {}
}
