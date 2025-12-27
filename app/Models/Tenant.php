<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'path',
    ];

    public function users()
    {
        return $this->hasMany(User::class)->withPivot('role')->withTimestamps();
    }
}
