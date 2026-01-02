<?php

namespace App\Domain\Authentication\Actions;

use Illuminate\Support\Facades\Auth;

class LogoutAction
{
    /**
     * 执行用户登出操作
     *
     * @return void
     */
    public function execute(): void
    {
        Auth::guard('web')->logout();
    }
}
