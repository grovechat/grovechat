<?php

namespace App\Data\User;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class FormUpdateUserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password = null,
        public ?string $avatar = null,
    ) {}

    public static function rules(): array
    {
        $userId = request()->route('id');

        return [
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
