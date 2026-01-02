<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Authentication\Actions\UpdatePasswordAction;
use App\Domain\Authentication\DTOs\UpdatePasswordData;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends Controller
{
    /**
     * Show the user's password settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/Password');
    }

    /**
     * Update the user's password.
     */
    public function update(
        Request $request,
        UpdatePasswordAction $updatePasswordAction
    ): RedirectResponse {
        $data = UpdatePasswordData::from($request->all());

        $updatePasswordAction->execute($request->user(), $data);

        return back();
    }
}
