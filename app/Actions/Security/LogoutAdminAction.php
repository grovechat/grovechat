<?php

namespace App\Actions\Security;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class LogoutAdminAction
{
    use AsAction;

    public function asController(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        if ($request->hasSession()) {
            $request->session()->regenerate();
            $request->session()->regenerateToken();
        }

        return redirect()->to(route('home', absolute: false));
    }
}
