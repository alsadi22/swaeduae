<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventApplication extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'user_id', 'status', 'additional_info', 'hours_logged'];

    public function event() {
        return $this->belongsTo(Event::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
