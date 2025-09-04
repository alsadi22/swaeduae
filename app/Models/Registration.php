<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = ['volunteer_id', 'event_id', 'status'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }
}
