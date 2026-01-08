<?php

namespace App\Models;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
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
        $slug = (new Hashids('', 0, 'abcdefghijklmnopqrstuvwxyz'))->encode($this->id);
        $this->update(['slug' => $slug]);
    }
}
