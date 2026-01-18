<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;

class SystemUserEditPagePropsData extends Data
{
    public function __construct(
        public SystemUserEditFormData $user_form,
    ) {}
}
