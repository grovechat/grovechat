<?php

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\LoginData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    /**
     * 执行用户登录操作
     *
     * @param LoginData $data
     * @return bool
     * @throws ValidationException
     */
    public function execute(LoginData $data): bool
    {
        $credentials = [
            'email' => $data->email,
            'password' => $data->password,
        ];

        if (!Auth::attempt($credentials, $data->remember)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return true;
    }
}
