<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SystemUserEditPagePropsData extends Data
{
    public function __construct(
        public SystemUserEditFormData $user_form,
    ) {}
}
