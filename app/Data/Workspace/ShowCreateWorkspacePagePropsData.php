<?php

namespace App\Data\Workspace;

use App\Data\User\UserOptionData;
use Spatie\LaravelData\Data;

class ShowCreateWorkspacePagePropsData extends Data
{
    public function __construct(
        /** @var UserOptionData[] */
        public array $owner_options,
    ) {}
}
