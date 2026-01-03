<?php

namespace App\Http\Controllers\Settings;

use App\Domain\User\Actions\DeleteAccountAction;
use App\Domain\User\Actions\UpdateProfileAction;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(
        Request $request,
        UpdateProfileAction $updateProfileAction
    ): RedirectResponse {
        $updateProfileAction->execute($request->user()->id, $request->only(['name', 'email']));

        $tenantPath = $request->route('tenant_path');
        return to_route('profile.edit', ['tenant_path' => $tenantPath]);
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(
        Request $request,
        DeleteAccountAction $deleteAccountAction
    ): RedirectResponse {
        $deleteAccountAction->execute($request->user()->id);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
