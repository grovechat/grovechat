<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class StorageSettings extends Settings
{
    public bool $enabled = false;  // false: local, true: cloud
    
    public ?string $disk; // 存储策略名称，如 's3', 'oss', 'cos'
    public ?string $key;       // Access Key
    public ?string $secret;    // Secret Key
    
    public ?string $bucket;    // 存储桶名称
    public ?string $region;    // 地域
    public ?string $endpoint;  // 自定义 Endpoint (Minio 或 OSS 私有节点必填)
    
    // 访问优化
    public ?string $url;       // CDN 地址或自定义域名 (如 https://cdn.example.com)
    public bool $path_style = false;   // 是否开启路径风格 (Minio 常用)
    
    public static function group(): string
    {
        return 'storage';
    }
}