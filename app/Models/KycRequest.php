<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycRequest extends Model
{
    protected $fillable = ['user_id','status','data','document_path'];
    protected $casts = ['data'=>'array'];

    public function user(){ return $this->belongsTo(\App\Models\User::class); }
}
