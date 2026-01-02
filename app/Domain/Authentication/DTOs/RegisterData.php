<?php

/**@typescript*/

namespace App\Domain\Authentication\DTOs;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Data;

class RegisterData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $password_confirmation,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:users', 'unique:tenants,path', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }
}
