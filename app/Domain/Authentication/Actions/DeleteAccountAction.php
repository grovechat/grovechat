<?php

namespace App\Domain\Authentication\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DeleteAccountAction
{
    /**
     * 删除用户账户
     *
     * @param User $user
     * @return void
     */
    public function execute(User $user): void
    {
        // 登出用户
        Auth::logout();

        // 删除用户
        $user->delete();
    }
}
