<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'cover_image',
        'published_at',
    ];

    protected $dates = ['published_at'];
}
