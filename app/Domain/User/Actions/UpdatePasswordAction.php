<?php

namespace App\Domain\User\Actions;

use App\Domain\User\DTOs\UpdatePasswordData;
use App\Models\User;

class UpdatePasswordAction
{
    /**
     * 更新用户密码
     *
     * @param int $userId
     * @param array $data
     * @return void
     */
    public function execute(int $userId, array $data): void
    {
        $validatedData = UpdatePasswordData::from($data);

        $user = User::findOrFail($userId);

        $user->update([
            'password' => $validatedData->password,
        ]);
    }
}
