<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','threshold'];

    public function volunteers()
    {
        return $this->belongsToMany(User::class, 'badge_user', 'badge_id', 'volunteer_id')->withTimestamps();
    }
}
