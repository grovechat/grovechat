<?php

namespace App\Enums;

enum WorkspaceRole: string
{
    case ADMIN = 'admin';
    case CUSTOMER_SERVICE = 'customer_service';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => '管理员',
            self::CUSTOMER_SERVICE => '客服',
        };
    }
}
