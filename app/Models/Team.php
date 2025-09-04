<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'leader_id',
        'license_number',
        'description',
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function volunteers()
    {
        return $this->belongsToMany(User::class, 'team_volunteer')->withTimestamps();
    }
}
