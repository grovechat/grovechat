<?php

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\UpdateProfileData;
use App\Models\User;

class UpdateProfileAction
{
    /**
     * 更新用户资料
     *
     * @param User $user
     * @param UpdateProfileData $data
     * @return void
     */
    public function execute(User $user, UpdateProfileData $data): void
    {
        $user->fill([
            'name' => $data->name,
            'email' => $data->email,
        ]);

        // 如果邮箱被修改，需要重新验证
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
    }
}
