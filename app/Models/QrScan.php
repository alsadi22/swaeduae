<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrScan extends Model
{
    protected $table = 'qr_scans';
    protected $fillable = [
        'user_id','opportunity_id','action','code','lat','lng','ip',
        'attendance_id','scanned_at','ip_address','user_agent'
    ];
    protected $casts = [
        'scanned_at' => 'datetime',
        'lat' => 'float',
        'lng' => 'float',
    ];
}
