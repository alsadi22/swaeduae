<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Media extends Model {
    protected $fillable=['disk','path','original_name','mime','size'];
    public function url(){ return \Storage::disk($this->disk)->url($this->path); }
}
