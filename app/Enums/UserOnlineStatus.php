<?php

namespace App\Enums;

enum UserOnlineStatus: int
{
    case ONLINE = 1;
    case OFFLINE = 0;

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => '在线',
            self::OFFLINE => '离线',
        };
    }
}
