<?php

namespace App\Domain\User\Actions;

use App\Domain\User\DTOs\UpdateProfileData;
use App\Models\User;

class UpdateProfileAction
{
    /**
     * 更新用户资料
     *
     * @param int $userId
     * @param array $data
     * @return void
     */
    public function execute(int $userId, array $data): void
    {
        $validatedData = UpdateProfileData::validateAndCreate($data, UpdateProfileData::rulesForUser($userId));

        $user = User::findOrFail($userId);

        $user->fill([
            'name' => $validatedData->name,
            'email' => $validatedData->email,
        ]);

        // 如果邮箱被修改，需要重新验证
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
    }
}
