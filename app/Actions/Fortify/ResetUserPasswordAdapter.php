<?php

namespace App\Actions\Fortify;

use App\Domain\Authentication\Actions\ResetPasswordAction;
use App\Domain\Authentication\DTOs\ResetPasswordData;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

/**
 * Fortify Adapter - 桥接 Fortify 和 DDD Architecture
 */
class ResetUserPasswordAdapter implements ResetsUserPasswords
{
    public function __construct(
        private ResetPasswordAction $resetPasswordAction
    ) {}

    public function reset($user, array $input)
    {
        $data = ResetPasswordData::from([
            'token' => $input['token'] ?? '',
            'email' => $input['email'] ?? '',
            'password' => $input['password'] ?? '',
            'password_confirmation' => $input['password_confirmation'] ?? '',
        ]);

        $this->resetPasswordAction->execute($data);
    }
}
