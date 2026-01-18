<?php

namespace App\Data\Teammate;

use Spatie\LaravelData\Data;

class CreateTeammatePagePropsData extends Data
{
    public function __construct(
        public CreateTeammateFormData $user_form,
        /** @var \App\Data\EnumOptionData[] */
        public array $role_options,
    ) {}
}
