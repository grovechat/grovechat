<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Authentication\Actions\ConfirmPasswordAction;
use App\Domain\Authentication\Actions\ResetPasswordAction;
use App\Domain\Authentication\Actions\SendPasswordResetLinkAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends Controller
{
    /**
     * 显示忘记密码页面
     */
    public function showForgotPasswordForm(Request $request): Response
    {
        return Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * 发送密码重置链接
     */
    public function sendResetLink(
        Request $request,
        SendPasswordResetLinkAction $sendPasswordResetLinkAction
    ): RedirectResponse {
        $result = $sendPasswordResetLinkAction->execute($request->all());

        return back()->with('status', $result['status']);
    }

    /**
     * 显示重置密码页面
     */
    public function showResetPasswordForm(Request $request): Response
    {
        return Inertia::render('auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * 重置密码
     */
    public function resetPassword(
        Request $request,
        ResetPasswordAction $resetPasswordAction
    ): RedirectResponse {
        $result = $resetPasswordAction->execute($request->all());

        return redirect()->route('login')->with('status', $result['status']);
    }

    /**
     * 确认用户密码
     */
    public function confirmPassword(
        Request $request,
        ConfirmPasswordAction $confirmPasswordAction
    ): RedirectResponse {
        $confirmPasswordAction->execute($request->user()->id, $request->all());

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended();
    }
}
