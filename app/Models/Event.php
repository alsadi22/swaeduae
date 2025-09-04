<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    // Keep this generous to avoid mass-assignment issues; adjust later if needed
    protected $fillable = [
        'title','description','location','starts_at','ends_at','status','slug',
        'created_at','updated_at'
    ];

    // Critical: ensure dates are Carbon so Blade can call diffInHours(), format(), etc.
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
    ];
}
