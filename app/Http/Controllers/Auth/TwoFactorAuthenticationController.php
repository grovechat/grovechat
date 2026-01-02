<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Authentication\Actions\ConfirmTwoFactorAction;
use App\Domain\Authentication\Actions\DisableTwoFactorAction;
use App\Domain\Authentication\Actions\EnableTwoFactorAction;
use App\Domain\Authentication\Actions\GenerateNewRecoveryCodesAction;
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

        return Inertia::render('settings/TwoFactor');
    }

    /**
     * 启用双因素认证
     */
    public function store(
        Request $request,
        EnableTwoFactorAction $enableTwoFactorAction
    ): JsonResponse {
        $enableTwoFactorAction->execute($request->user());

        return response()->json(['two_factor_secret' => $request->user()->two_factor_secret]);
    }

    /**
     * 确认双因素认证
     */
    public function confirm(
        Request $request,
        ConfirmTwoFactorAction $confirmTwoFactorAction
    ): JsonResponse {
        $request->validate([
            'code' => 'required|string',
        ]);

        try {
            $confirmTwoFactorAction->execute($request->user(), $request->input('code'));

            return response()->json(['confirmed' => true]);
        } catch (ValidationException $e) {
            return response()->json(['confirmed' => false], 422);
        }
    }

    /**
     * 禁用双因素认证
     */
    public function destroy(
        Request $request,
        DisableTwoFactorAction $disableTwoFactorAction
    ): JsonResponse {
        $disableTwoFactorAction->execute($request->user());

        return response()->json(['disabled' => true]);
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
        return response()->json([
            'recovery_codes' => json_decode(decrypt($request->user()->two_factor_recovery_codes), true),
        ]);
    }

    /**
     * 生成新的恢复码
     */
    public function generateRecoveryCodes(
        Request $request,
        GenerateNewRecoveryCodesAction $generateNewRecoveryCodesAction
    ): JsonResponse {
        $generateNewRecoveryCodesAction->execute($request->user());

        return response()->json([
            'recovery_codes' => json_decode(decrypt($request->user()->two_factor_recovery_codes), true),
        ]);
    }
}
