<?php

namespace App\Models;

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
        /** @var Storage */
        $disk = Storage::disk($this->disk);

        return $disk->url($this->path);
    }
}
