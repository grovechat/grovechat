<?php

namespace App\Enums;

use App\Contracts\LabeledEnum;

enum WorkspaceRole: string implements LabeledEnum
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case CUSTOMER_SERVICE = 'customer_service';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => __('workspace.roles.owner'),
            self::ADMIN => __('workspace.roles.admin'),
            self::CUSTOMER_SERVICE => __('workspace.roles.customer_service'),
        };
    }

    /**
     * @return array<int, self>
     */
    public static function assignableCases(): array
    {
        return [
            self::ADMIN,
            self::CUSTOMER_SERVICE,
        ];
    }
}
