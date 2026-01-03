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
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function execute(array $data): array
    {
        $validatedData = LoginData::from($data);

        $credentials = [
            'email' => $validatedData->email,
            'password' => $validatedData->password,
        ];

        if (!Auth::attempt($credentials, $validatedData->remember)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return ['success' => true];
    }
}
