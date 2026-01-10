<?php

namespace App\Data;

use Spatie\LaravelData\Data;

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
    
    public function rule()
    {
        return [
            'enabled' => 'required|boolean',
            'provider' => 'required_if:enabled,true|string|in:s3,oss',
            'key' => 'required_if:enabled,true|string',
            'secret' => 'required_if:enabled,true|string',
            'bucket' => 'required_if:enabled,true|string',
            'region' => 'required_if:enabled,true|string',
            'endpoint' => 'required_if:enabled,true|string|url',
            'url' => 'nullable|string|url',
        ];
    }
}
