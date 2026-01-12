<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class StorageProfileUpdateData extends Data
{
    public function __construct(
        public string $name,
        public ?string $url = null,
        public ?string $key = null,
        public ?string $secret = null,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:64'],
            'url' => ['nullable', 'string', 'url'],
            // 更新凭证：允许不填；具体“必须 key+secret 成对”由 handle 里做业务校验（可处理空字符串）
            'key' => ['nullable', 'string'],
            'secret' => ['nullable', 'string'],
        ];
    }
}

