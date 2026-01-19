<?php

namespace App\Models;

use App\Services\Storage\StorageProfileDisk;
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
        return $this->filesystem()->url($this->path);
    }

    public function storageProfile()
    {
        return $this->belongsTo(StorageProfile::class, 'storage_profile_id');
    }

    public function filesystem()
    {
        if ($this->storage_profile_id) {
            $profile = $this->relationLoaded('storageProfile')
                ? $this->storageProfile
                : $this->storageProfile()->first();

            if ($profile) {
                return StorageProfileDisk::build($profile);
            }
        }

        return Storage::disk($this->disk);
    }
}
