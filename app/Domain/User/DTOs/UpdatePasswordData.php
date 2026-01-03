<?php

/**@typescript*/

namespace App\Domain\User\DTOs;

use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Data;

class UpdatePasswordData extends Data
{
    public function __construct(
        public string $current_password,
        public string $password,
        public string $password_confirmation,
    ) {}

    public static function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }
}
