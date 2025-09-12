<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificates';
    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Optional: link to events when certificates.event_id exists
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // Optional: some certs may be tied to an opportunity instead
    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class, 'opportunity_id');
    }
}
