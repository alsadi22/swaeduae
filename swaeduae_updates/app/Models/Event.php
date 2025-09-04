<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Event model represents events hosted by organisations.
 *
 * The `$fillable` property defines which attributes can be mass‑assigned. The
 * `$dates` property instructs Eloquent to cast the `date` attribute to a
 * Carbon instance for convenient date operations.
 */
class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'image',
        'status',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates = ['date'];
}