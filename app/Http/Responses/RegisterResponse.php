<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): Response
    {
        /** @var Request $request */
        $user = $request->user();

        if ($user && $user->is_super_admin) {
            return redirect()->intended(route('admin.home', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
