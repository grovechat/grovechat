<?php

namespace App\Domain\Authentication\Actions;

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
     * @param User $user
     * @return void
     */
    public function execute(User $user): void
    {
        $this->disable($user);
    }
}
