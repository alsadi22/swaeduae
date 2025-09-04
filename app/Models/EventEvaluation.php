<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventEvaluation extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'user_id', 'rating', 'comments', 'reflection'];

    public function event() {
        return $this->belongsTo(Event::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
