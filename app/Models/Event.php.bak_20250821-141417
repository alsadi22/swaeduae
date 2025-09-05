<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::saving(function ($e) {
            if (empty($e->slug)) {
                $base = Str::slug($e->title ?: ($e->name ?: 'event'));
                if ($base === '') {
                    $base = 'event';
                }

                $slug = $base;
                $i = 1;

                $query = static::query();
                if ($e->exists && $e->getKey()) {
                    $query->whereKeyNot($e->getKey());
                }

                while ((clone $query)->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }

                $e->slug = $slug;
            }
        });
    }
}
