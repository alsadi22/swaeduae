<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerProfile extends Model
{
    protected $fillable = [
        'user_id','name_ar','gender','dob','nationality','emirate','city',
        'emirates_id','emirates_id_expiry','avatar_path','skills','interests','availability'
    ];
    protected $casts = [
        'dob' => 'date',
        'emirates_id_expiry' => 'date',
        'skills' => 'array',
        'interests' => 'array',
        'availability' => 'array',
    ];
    public function user(): BelongsTo { return $this->belongsTo(\App\Models\User::class); }
}
