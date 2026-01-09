<?php

namespace App\Models;

use Godruoyi\Snowflake\Snowflake;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'path',
        'owner_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function createSlug()
    {
        $this->update(['slug' => (new Snowflake())->id()]);
    }
}
