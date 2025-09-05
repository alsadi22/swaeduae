<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
    protected $fillable = ['user_id','provider','currency','amount','status','provider_id','metadata'];
    protected $casts = ['metadata'=>'array'];
    public function user(){ return $this->belongsTo(\App\Models\User::class); }
    public function payable(){ return $this->morphTo(); }
    public function scopePaid($q){ return $q->where('status','paid'); }
}

