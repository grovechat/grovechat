<?php

namespace App\Data\Teammate;

use Spatie\LaravelData\Data;

class ShowEditTeammatePagePropsData extends Data
{
    public function __construct(
        public TeammateData $user_form,
        /** @var \App\Data\EnumOptionData[] */
        public array $role_options,
        public bool $can_update_profile,
        public bool $can_update_email,
        public bool $can_update_password,
        public bool $can_update_role,
    ) {}
}
