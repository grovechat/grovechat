<?php

namespace App\Data;

use App\Enums\StorageProvider;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class StorageSettingCheckData extends Data
{
    public function __construct(
        public string $provider,
        public string $key,
        public ?string $secret,
        public string $bucket,
        public string $region,
        public string $endpoint,
        public ?string $url = null,
    ) {}

    public static function rules(): array
    {
        $providers = array_map(static fn (StorageProvider $p) => $p->value, StorageProvider::cases());

        return [
            'provider' => ['required', 'string', Rule::in($providers)],
            'key' => ['required', 'string'],
            'secret' => ['nullable', 'string'],
            'bucket' => ['required', 'string'],
            'region' => ['required', 'string'],
            'endpoint' => ['required', 'string', 'url'],
            'url' => ['nullable', 'string', 'url'],
        ];
    }
}
