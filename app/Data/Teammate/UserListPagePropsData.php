<?php

namespace App\Data\Teammate;

use Spatie\LaravelData\Data;

class UserListPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\WorkspaceUserContextData[] */
        public array $user_list,
        
        /** @var \App\Data\EnumOptionData[] */
        public array $online_status_options,
        
        public bool $can_restore_user,
    ) {}
}
