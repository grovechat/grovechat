<?php

namespace App\Domain\TwoFactor\Actions;

use App\Models\User;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;

class DisableTwoFactorAction
{
    public function __construct(
        private DisableTwoFactorAuthentication $disable
    ) {}

    /**
     * 禁用双因素认证
     *
     * @param int $userId
     * @return void
     */
    public function execute(int $userId): void
    {
        $user = User::findOrFail($userId);
        ($this->disable)($user);
    }
}
