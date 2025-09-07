<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationPing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_id',
        'lat',
        'lng',
        'accuracy',
        'captured_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
    ];
}
