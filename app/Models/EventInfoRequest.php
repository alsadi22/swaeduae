<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventInfoRequest extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'form_fields'];

    protected $casts = [
        'form_fields' => 'array',
    ];

    public function event() {
        return $this->belongsTo(Event::class);
    }
}
