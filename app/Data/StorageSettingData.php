<?php

namespace App\Data;

use App\Enums\StorageProvider;
use Spatie\LaravelData\Data;
use Illuminate\Validation\Rule;

class StorageSettingData extends Data
{
    public function __construct(
        public bool $enabled,
        public ?string $provider,
        public ?string $key,
        public ?string $secret,  
        
        public ?string $bucket,  
        public ?string $region,
        public ?string $endpoint, 
        
        public ?string $url,
    ) {}
    
    public static function rules(): array
    {
        $providers = array_map(static fn (StorageProvider $p) => $p->value, StorageProvider::cases());

        return [
            'enabled' => 'required|boolean',
            'provider' => ['required_if:enabled,true', 'string', Rule::in($providers)],
            'key' => 'required_if:enabled,true|string',
            // Update 场景允许留空（表示保持原值）；是否必须由业务逻辑（首次启用/是否已有 secret）决定
            'secret' => 'nullable|string',
            'bucket' => 'required_if:enabled,true|string',
            'region' => 'required_if:enabled,true|string',
            'endpoint' => 'required_if:enabled,true|string|url',
            'url' => 'nullable|string|url',
        ];
    }
}
