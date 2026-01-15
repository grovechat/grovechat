<?php

namespace App\Enums;

enum WorkspaceRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case CUSTOMER_SERVICE = 'customer_service';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => __('workspace_roles.owner'),
            self::ADMIN => __('workspace_roles.admin'),
            self::CUSTOMER_SERVICE => __('workspace_roles.customer_service'),
        };
    }

    /**
     * @return array<int, self>
     */
    public static function assignableCases(): array
    {
        return [
            self::OWNER,
            self::ADMIN,
            self::CUSTOMER_SERVICE,
        ];
    }
}
