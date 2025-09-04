<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    /**
     * Mass-assignable fields.
     */
    protected $fillable = [
        'uuid','user_id','opportunity_id','event_id','code',
        'signature','pdf_path','hours','issued_at','revoked_at'
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
        'hours'     => 'decimal:2',
    ];

    protected $table = 'certificates';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class, 'opportunity_id');
    }
}
