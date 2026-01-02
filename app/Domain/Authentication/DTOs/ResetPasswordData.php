<?php

/**@typescript*/

namespace App\Domain\Authentication\DTOs;

use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Data;

class ResetPasswordData extends Data
{
    public function __construct(
        public string $token,
        public string $email,
        public string $password,
        public string $password_confirmation,
    ) {}

    public static function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }
}
