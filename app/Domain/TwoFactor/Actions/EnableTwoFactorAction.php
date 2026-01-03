<?php

namespace App\Domain\TwoFactor\Actions;

use App\Models\User;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;

class EnableTwoFactorAction
{
    public function __construct(
        private EnableTwoFactorAuthentication $enable
    ) {}

    /**
     * 启用双因素认证
     *
     * @param int $userId
     * @return void
     */
    public function execute(int $userId): void
    {
        $user = User::findOrFail($userId);
        ($this->enable)($user);
    }
}
