<?php

namespace App\Data\Teammate;

use App\Enums\UserOnlineStatus;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class UpdateTeammateOnlineStatusData extends Data
{
    public function __construct(
        public UserOnlineStatus $online_status,
    ) {}

    public static function rules(): array
    {
        $values = array_map(static fn (UserOnlineStatus $s) => $s->value, UserOnlineStatus::cases());

        return [
            'online_status' => ['required', 'integer', Rule::in($values)],
        ];
    }
}
