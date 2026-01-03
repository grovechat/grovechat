<?php

namespace App\Http\Controllers\Auth;

use App\Domain\TwoFactor\Actions\ConfirmTwoFactorAction;
use App\Domain\TwoFactor\Actions\DisableTwoFactorAction;
use App\Domain\TwoFactor\Actions\EnableTwoFactorAction;
use App\Domain\TwoFactor\Actions\GenerateNewRecoveryCodesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * 显示双因素认证设置页面
     */
    public function show(Request $request): Response|RedirectResponse
    {
        if (!Features::enabled(Features::twoFactorAuthentication())) {
            return redirect()->route('profile.edit', ['tenant_path' => $request->route('tenant_path')]);
        }

        $user = $request->user();

        return Inertia::render('settings/TwoFactor', [
            'requiresConfirmation' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
            'twoFactorEnabled' => !is_null($user->two_factor_confirmed_at),
        ]);
    }

    /**
     * 启用双因素认证
     */
    public function store(
        Request $request,
        EnableTwoFactorAction $enableTwoFactorAction
    ): RedirectResponse {
        $enableTwoFactorAction->execute($request->user()->id);

        return back();
    }

    /**
     * 确认双因素认证
     */
    public function confirm(
        Request $request,
        ConfirmTwoFactorAction $confirmTwoFactorAction
    ): RedirectResponse {
        $confirmTwoFactorAction->execute($request->user()->id, $request->all());

        return back();
    }

    /**
     * 禁用双因素认证
     */
    public function destroy(
        Request $request,
        DisableTwoFactorAction $disableTwoFactorAction
    ): RedirectResponse {
        $disableTwoFactorAction->execute($request->user()->id);

        return back();
    }

    /**
     * 获取设置密钥
     */
    public function secretKey(Request $request): JsonResponse
    {
        return response()->json([
            'secretKey' => decrypt($request->user()->two_factor_secret),
        ]);
    }

    /**
     * 获取 QR 码
     */
    public function qrCode(Request $request): JsonResponse
    {
        return response()->json([
            'svg' => $request->user()->twoFactorQrCodeSvg(),
        ]);
    }

    /**
     * 获取恢复码
     */
    public function recoveryCodes(Request $request): JsonResponse
    {
        return response()->json(
            json_decode(decrypt($request->user()->two_factor_recovery_codes), true)
        );
    }

    /**
     * 生成新的恢复码
     */
    public function generateRecoveryCodes(
        Request $request,
        GenerateNewRecoveryCodesAction $generateNewRecoveryCodesAction
    ): RedirectResponse {
        $generateNewRecoveryCodesAction->execute($request->user()->id);

        return back();
    }
}
