<?php

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\ForgotPasswordData;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class SendPasswordResetLinkAction
{
    /**
     * 发送密码重置链接
     *
     * @param ForgotPasswordData $data
     * @return string 状态消息
     * @throws ValidationException
     */
    public function execute(ForgotPasswordData $data): string
    {
        $status = Password::sendResetLink(
            ['email' => $data->email]
        );

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }
}
