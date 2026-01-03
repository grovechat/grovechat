<?php

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\ResetPasswordData;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordAction
{
    /**
     * 重置用户密码
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function execute(array $data): array
    {
        $validatedData = ResetPasswordData::from($data);

        $status = Password::reset(
            [
                'email' => $validatedData->email,
                'password' => $validatedData->password,
                'password_confirmation' => $validatedData->password_confirmation,
                'token' => $validatedData->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return ['status' => __($status)];
    }
}
