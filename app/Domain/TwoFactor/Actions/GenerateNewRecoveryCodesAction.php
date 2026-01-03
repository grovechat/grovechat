<?php

namespace App\Domain\TwoFactor\Actions;

use App\Models\User;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;

class GenerateNewRecoveryCodesAction
{
    public function __construct(
        private GenerateNewRecoveryCodes $generate
    ) {}

    /**
     * 生成新的恢复码
     *
     * @param int $userId
     * @return void
     */
    public function execute(int $userId): void
    {
        $user = User::findOrFail($userId);
        ($this->generate)($user);
    }
}
