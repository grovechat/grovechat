<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $workspace_id
 * @property string $name
 * @property string|null $color
 * @property string|null $description
 * @property mixed $use_factory
 * @property int|null $workspaces_count
 *
 * @property-read \App\Models\Workspace $workspace
 *
 * @method static \Database\Factories\TagFactory<self> factory($count = null, $state = [])
 */
class Tag extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    
    protected $table = 'tags';

    protected $guarded = [];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
