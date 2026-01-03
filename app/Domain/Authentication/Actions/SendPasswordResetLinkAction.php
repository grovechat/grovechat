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
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function execute(array $data): array
    {
        $validatedData = ForgotPasswordData::from($data);

        $status = Password::sendResetLink(
            ['email' => $validatedData->email]
        );

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return ['status' => __($status)];
    }
}
