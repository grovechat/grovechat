<?php

namespace App\Domain\TwoFactor\Actions;

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
     * @param int $userId
     * @param array $data
     * @return void
     */
    public function execute(int $userId, array $data): void
    {
        $user = User::findOrFail($userId);
        ($this->confirm)($user, $data['code']);
    }
}
