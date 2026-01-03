<?php

namespace App\Domain\User\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DeleteAccountAction
{
    /**
     * 删除用户账户
     *
     * @param int $userId
     * @return void
     */
    public function execute(int $userId): void
    {
        $user = User::findOrFail($userId);

        // 登出用户
        Auth::logout();

        // 删除用户
        $user->delete();
    }
}
