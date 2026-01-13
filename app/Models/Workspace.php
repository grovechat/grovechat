<?php

namespace App\Models;

use App\Actions\Attachment\BindAttachmentModelAction;
use App\Actions\Attachment\DeleteAttachmentAction;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workspace extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

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

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
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
                if ($attachment = Attachment::query()->find($workspace->logo_id)) {
                    if (!empty($workspace->getOriginal('logo_id'))) {
                        if ($oldAttachment = Attachment::query()->find($workspace->getOriginal('logo_id'))) {
                            DeleteAttachmentAction::run($oldAttachment);
                        }
                    }
                    BindAttachmentModelAction::run($attachment, $workspace);
                }
            }
        });

        static::created(function ($workspace) {
            if ($workspace->logo_id) {
                if ($attachment = Attachment::query()->find($workspace->logo_id)) {
                    BindAttachmentModelAction::run($attachment, $workspace);
                }
            }
        });
    }
}
