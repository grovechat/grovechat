<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Authentication\Actions\DeleteAccountAction;
use App\Domain\Authentication\Actions\UpdateProfileAction;
use App\Domain\Authentication\DTOs\ConfirmPasswordData;
use App\Domain\Authentication\DTOs\UpdateProfileData;
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
        $data = UpdateProfileData::validateAndCreate([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ], UpdateProfileData::rulesForUser($request->user()->id));

        $updateProfileAction->execute($request->user(), $data);

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
        $passwordData = ConfirmPasswordData::from($request->all());

        $deleteAccountAction->execute($request->user());

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
