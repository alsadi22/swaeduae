<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'name_en', 'name_ar', 'emirate', 'org_type', 'logo_path',
        'email', 'public_email', 'mobile', 'mobile_verified_at', 'website', 'address',
        'description', 'volunteer_programs',
        'contact_person_name', 'contact_person_email', 'contact_person_phone',
        'wants_license', 'license_status', 'license_file_path', 'license_number', 'review_notes',
        'tos_accepted_at', 'policy_accepted_at',
        'owner_id',
    ];

    protected $casts = [
        'mobile_verified_at' => 'datetime',
        'wants_license' => 'boolean',
        'tos_accepted_at' => 'datetime',
        'policy_accepted_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
