<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string $provider
 * @property string|null $key
 * @property string|null $secret
 * @property string|null $bucket
 * @property string|null $region
 * @property string|null $endpoint
 * @property string|null $url
 */
class StorageProfile extends Model
{
    use HasUlids;

    protected $table = 'storage_profiles';

    protected $guarded = [];

    protected $casts = [
        'key' => 'encrypted',
        'secret' => 'encrypted',
    ];
}
