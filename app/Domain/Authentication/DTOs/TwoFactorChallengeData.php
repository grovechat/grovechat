<?php

/**@typescript*/

namespace App\Domain\Authentication\DTOs;

use Spatie\LaravelData\Data;

class TwoFactorChallengeData extends Data
{
    public function __construct(
        public ?string $code = null,
        public ?string $recovery_code = null,
    ) {}

    public static function rules(): array
    {
        return [
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ];
    }
}
