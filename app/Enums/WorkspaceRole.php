<?php

namespace App\Enums;

use App\Contracts\LabeledEnum;

enum WorkspaceRole: string implements LabeledEnum
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case OPERATOR = 'operator';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => __('workspace.roles.owner'),
            self::ADMIN => __('workspace.roles.admin'),
            self::OPERATOR => __('workspace.roles.operator'),
        };
    }

    /**
     * @return array<int, self>
     */
    public static function assignableCases(): array
    {
        return [
            self::ADMIN,
            self::OPERATOR,
        ];
    }
}
