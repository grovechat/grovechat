<?php

namespace App\Domain\Authentication\Actions;

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
     * @param User $user
     * @return void
     */
    public function execute(User $user): void
    {
        $this->generate($user);
    }
}
