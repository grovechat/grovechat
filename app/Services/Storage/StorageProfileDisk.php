<?php

namespace App\Services\Storage;

use App\Enums\StorageProvider;
use App\Models\StorageProfile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageProfileDisk
{
    public static function build(StorageProfile $profile)
    {
        $provider = $profile->provider ? StorageProvider::tryFrom($profile->provider) : null;

        $config = [
            'driver' => 's3',
            'key' => $profile->key,
            'secret' => $profile->secret,
            'region' => $profile->region ?: 'us-east-1',
            'bucket' => $profile->bucket,
            'endpoint' => $profile->endpoint,
            'url' => $profile->url,
            'use_path_style_endpoint' => $provider === StorageProvider::MINIO,
            'throw' => true,
        ];

        // 避免把 null 传给底层 adapter（兼容性更好）
        $config = collect($config)->reject(fn ($v) => $v === null)->all();

        // Storage::build 会返回一个临时 FilesystemAdapter，不污染全局 config
        return Storage::build($config);
    }

    public static function randomPrefix(): string
    {
        // 预留：未来如果要把 root/prefix 放进 config，可使用这个方法隔离
        return 'sp_'.Str::random(8);
    }
}
