<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
  protected $fillable = ['key','value'];
  public static function getValue(string $key, $default=null){
    $row = static::query()->where('key',$key)->first();
    return $row ? $row->value : $default;
  }
  // back-compat alias (avoid Builder::get() collision)
  public static function get(string $key, $default=null){ return static::getValue($key,$default); }
  public static function putValue(string $key, $value){ return static::updateOrCreate(['key'=>$key],['value'=>$value]); }
  public static function put(string $key, $value){ return static::putValue($key,$value); }
}
