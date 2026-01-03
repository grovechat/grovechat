<?php

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\ConfirmPasswordData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ConfirmPasswordAction
{
    /**
     * 确认用户密码
     *
     * @param int $userId
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function execute(int $userId, array $data): array
    {
        $validatedData = ConfirmPasswordData::from($data);

        $user = User::findOrFail($userId);

        if (!Hash::check($validatedData->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => [__('auth.password')],
            ]);
        }

        return ['confirmed' => true];
    }
}
