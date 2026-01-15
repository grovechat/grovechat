<?php

namespace App\Enums;

use App\Contracts\LabeledEnum;

enum UserOnlineStatus: int implements LabeledEnum
{
    case ONLINE = 1;
    case OFFLINE = 0;

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => __('user.online_statuses.online'),
            self::OFFLINE => __('user.online_statuses.offline'),
        };
    }
}
