<?php

namespace App\Models;

use App\Services\AttachmentService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Workspace extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    protected $fillable = [
        'id',
        'name',
        'slug',
        'logo_id',
        'owner_id',
    ];

    protected $appends = [
        'logo_url',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function logo()
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->logo?->full_url ?? asset('images/workspace.png'),
        );
    }

    protected static function booted()
    {
        static::updated(function (Model $workspace) {
            if ($workspace->wasChanged('logo_id') && $workspace->logo_id) {
                AttachmentService::replace($workspace->getOriginal('logo_id'), $workspace->logo_id, $workspace);
            }
        });

        static::created(function ($workspace) {
            if ($workspace->logo_id) {
                AttachmentService::bind($workspace->logo_id, $workspace);
            }
        });
    }

    public function createSlug()
    {
        $this->update(['slug' => Str::ulid()]);
    }
}
