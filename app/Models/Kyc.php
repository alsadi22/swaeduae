<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{
    protected $fillable = ['user_id', 'status', 'document'];
    public function user() {
        return $this->belongsTo(User::class);
    }
}
