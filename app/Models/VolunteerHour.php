<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerHour extends Model
{
    protected $table = 'volunteer_hours';
    protected $fillable = ['user_id','opportunity_id','hours','note'];
}
