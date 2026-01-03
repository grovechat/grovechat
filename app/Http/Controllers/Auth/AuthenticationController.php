<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Authentication\Actions\LoginAction;
use App\Domain\Authentication\Actions\LogoutAction;
use App\Domain\Authentication\Actions\RegisterAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class AuthenticationController extends Controller
{
    /**
     * 显示登录页面
     */
    public function showLoginForm(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Features::enabled(Features::resetPasswords()),
            'canRegister' => Features::enabled(Features::registration()),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * 处理登录请求
     */
    public function login(
        Request $request,
        LoginAction $loginAction
    ): RedirectResponse {
        $loginAction->execute($request->all());

        $request->session()->regenerate();

        return redirect()->intended(config('fortify.home'));
    }

    /**
     * 显示注册页面
     */
    public function showRegisterForm(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * 处理注册请求
     */
    public function register(
        Request $request,
        RegisterAction $registerAction
    ): RedirectResponse {
        $result = $registerAction->execute($request->all());

        Auth::loginUsingId($result['user_id']);

        $request->session()->regenerate();

        return redirect(config('fortify.home'));
    }

    /**
     * 处理登出请求
     */
    public function logout(
        Request $request,
        LogoutAction $logoutAction
    ): RedirectResponse {
        $logoutAction->execute();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
