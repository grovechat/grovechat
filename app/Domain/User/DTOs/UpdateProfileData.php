<?php

/**@typescript*/

namespace App\Domain\User\DTOs;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class UpdateProfileData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ];
    }

    /**
     * 动态验证规则 (需要当前用户 ID)
     */
    public static function rulesForUser(int $userId): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
        ];
    }
}
