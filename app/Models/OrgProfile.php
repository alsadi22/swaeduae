<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrgProfile extends Model
{
    protected $fillable = [
        'user_id','org_name','emirate','phone','website','about','status','approved_at','admin_notes'
    ];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
