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
     * @param User $user
     * @param ConfirmPasswordData $data
     * @return bool
     * @throws ValidationException
     */
    public function execute(User $user, ConfirmPasswordData $data): bool
    {
        if (!Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => [__('auth.password')],
            ]);
        }

        return true;
    }
}
