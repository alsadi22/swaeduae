<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationRegistration extends Model
{
    protected $fillable = [
        'user_id','organization_name','trade_license_number','phone','website',
        'emirate','city','address','contact_person_name','contact_person_email',
        'contact_person_phone','sector','description','status'
    ];
}
