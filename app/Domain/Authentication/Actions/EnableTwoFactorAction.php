<?php

namespace App\Domain\Authentication\Actions;

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
     * @param User $user
     * @return void
     */
    public function execute(User $user): void
    {
        ($this->enable)($user);
    }
}
