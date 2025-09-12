<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AdminAction extends Model
{
    protected $fillable = ['admin_id','action','subject_type','subject_id','meta'];
    protected $casts = ['meta' => 'array'];
}
