<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationPing extends Model
{
    protected $fillable = [
        'user_id', 'lat', 'lng', 'accuracy',
    ];

    protected $casts = [
        'user_id'  => 'integer',
        'lat'      => 'float',
        'lng'      => 'float',
        'accuracy' => 'float',
    ];
}
