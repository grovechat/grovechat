<?php

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\UpdatePasswordData;
use App\Models\User;

class UpdatePasswordAction
{
    /**
     * 更新用户密码
     *
     * @param User $user
     * @param UpdatePasswordData $data
     * @return void
     */
    public function execute(User $user, UpdatePasswordData $data): void
    {
        $user->update([
            'password' => $data->password,
        ]);
    }
}
