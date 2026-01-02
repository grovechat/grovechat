<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Authentication\Actions\ResetPasswordAction;
use App\Domain\Authentication\Actions\SendPasswordResetLinkAction;
use App\Domain\Authentication\DTOs\ForgotPasswordData;
use App\Domain\Authentication\DTOs\ResetPasswordData;
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
        $data = ForgotPasswordData::from($request->all());

        try {
            $status = $sendPasswordResetLinkAction->execute($data);

            return back()->with('status', $status);
        } catch (ValidationException $e) {
            throw $e;
        }
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
        $data = ResetPasswordData::from($request->all());

        try {
            $status = $resetPasswordAction->execute($data);

            return redirect()->route('login')->with('status', $status);
        } catch (ValidationException $e) {
            throw $e;
        }
    }
}
