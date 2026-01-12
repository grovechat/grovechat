<?php

namespace App\Models;

use App\Enums\StorageProvider;
use App\Settings\StorageSettings;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasUlids;

    protected $guarded = [];

    protected $appends = [
        'full_url',
    ];

    // 多态关联
    public function attachable()
    {
        return $this->morphTo();
    }

    // 获取完整 URL 的访问器
    public function getFullUrlAttribute()
    {
        self::setFilesystem();
        
        /** @var Storage */
        $disk = Storage::disk($this->disk);

        return $disk->url($this->path);
    }
    
    public static function setFilesystem()
    {
        $settings = app(StorageSettings::class);
        if (! $settings->enabled) {
            config(['filesystems.default' => 'public']);
            return;
        }

        config([
            'filesystems.default' => 's3',
            'filesystems.disks.s3' => [
                'driver' => 's3',
                'key' => $settings->key,
                'secret' => $settings->secret,
                'region' => $settings->region ?? 'us-east-1',
                'bucket' => $settings->bucket,
                'endpoint' => $settings->endpoint,
                'url' => $settings->url,
                'use_path_style_endpoint' => $settings->provider == StorageProvider::MINIO,
            ],
        ]);
    }
}
