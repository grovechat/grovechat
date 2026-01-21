<?php

namespace App\Data\Teammate;

use App\Enums\UserOnlineStatus;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class FormUpdateTeammateOnlineStatusData extends Data
{
    public function __construct(
        public UserOnlineStatus $online_status,
    ) {}

    public static function rules(): array
    {
        return [
            'online_status' => ['required', 'integer', Rule::enum(UserOnlineStatus::class)],
        ];
    }
}
