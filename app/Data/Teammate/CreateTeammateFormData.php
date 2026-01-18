<?php

namespace App\Data\Teammate;

use App\Enums\UserOnlineStatus;
use App\Enums\WorkspaceRole;
use Spatie\LaravelData\Data;

class CreateTeammateFormData extends Data
{
    public function __construct(
        public string $name = '',
        public ?string $nickname = null,
        public ?string $avatar = null,
        public string $email = '',
        public WorkspaceRole $role = WorkspaceRole::OPERATOR,
        public UserOnlineStatus $online_status = UserOnlineStatus::ONLINE,
    ) {}
}
