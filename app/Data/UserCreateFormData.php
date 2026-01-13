<?php

namespace App\Data;

use App\Enums\UserOnlineStatus;
use App\Enums\WorkspaceRole;
use Spatie\LaravelData\Data;

class UserCreateFormData extends Data
{
    public function __construct(
        public string $name = '',
        public ?string $external_nickname = null,
        public ?string $avatar = null,
        public string $email = '',
        public WorkspaceRole $role = WorkspaceRole::CUSTOMER_SERVICE,
        public UserOnlineStatus $online_status = UserOnlineStatus::ONLINE,
    ) {}
}
