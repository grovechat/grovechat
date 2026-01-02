<?php

namespace App\Domain\Authentication\Actions;

use App\Models\User;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;

class ConfirmTwoFactorAction
{
    public function __construct(
        private ConfirmTwoFactorAuthentication $confirm
    ) {}

    /**
     * 确认双因素认证
     *
     * @param User $user
     * @param string $code
     * @return void
     */
    public function execute(User $user, string $code): void
    {
        ($this->confirm)($user, $code);
    }
}
