<?php

/**@typescript*/

namespace App\Domain\Authentication\DTOs;

use Spatie\LaravelData\Data;

class ConfirmPasswordData extends Data
{
    public function __construct(
        public string $password,
    ) {}

    public static function rules(): array
    {
        return [
            'password' => ['required', 'current_password'],
        ];
    }
}
