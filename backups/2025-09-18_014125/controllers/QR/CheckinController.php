<?php
namespace App\Http\Controllers\QR;
use App\Http\Controllers\Controller; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB; use Illuminate\Support\Carbon;

class CheckinController extends Controller {
  public function checkin(Request $r){
    $u=$r->user(); $d=$r->validate(['event_id'=>'required|integer','opportunity_id'=>'nullable|integer','token'=>'nullable|string|max:64','lat'=>'nullable|numeric','lng'=>'nullable|numeric']);
    $now=Carbon::now(); $attId=DB::table('attendances')->insertGetId([
      'user_id'=>$u->id,'opportunity_id'=>$d['opportunity_id']??null,'event_id'=>$d['event_id'],
      'check_in_at'=>$now,'source'=>'qr','lat'=>$d['lat']??null,'lng'=>$d['lng']??null,'status'=>'ok','created_at'=>$now,'updated_at'=>$now,
    ]);
    if(!empty($d['token'])){ DB::table('qr_scans')->insert([
      'token_id'=>DB::table('qr_tokens')->where('token',$d['token'])->value('id'),'user_id'=>$u->id,
      'lat'=>$d['lat']??null,'lng'=>$d['lng']??null,'scanned_at'=>$now,'device'=>json_encode(['ua'=>$r->userAgent()]),
      'created_at'=>$now,'updated_at'=>$now,
    ]); }
    return response()->json(['ok'=>true,'attendance_id'=>$attId,'at'=>$now->toIso8601String()]);
  }

  public function checkout(Request $r){
    $u=$r->user(); $d=$r->validate(['attendance_id'=>'required|integer','lat'=>'nullable|numeric','lng'=>'nullable|numeric']);
    $att=DB::table('attendances')->where('id',$d['attendance_id'])->where('user_id',$u->id)->first();
    if(!$att || $att->check_out_at){ return response()->json(['ok'=>false,'error'=>'invalid_or_closed'],422); }
    $now=Carbon::now(); $raw=(int)floor(($now->diffInSeconds(Carbon::parse($att->check_in_at)))/60);
    $round=(int)config('hours.round_to_min',5); $minOk=(int)config('hours.min_eligible_min',15); $clip=(bool)config('hours.clip_to_shift',true);
    $aw=$raw;
    if($clip){
      $in=Carbon::parse($att->check_in_at); $out=$now->copy(); $sum=0;
      $sh=DB::table('shifts')->where('event_id',$att->event_id)->get(['starts_at','ends_at']);
      if($sh->count()){
        foreach($sh as $s){ $S=Carbon::parse($s->starts_at); $E=Carbon::parse($s->ends_at);
          $os=$in->max($S); $oe=$out->min($E); if($oe->gt($os)) $sum += (int)floor($oe->diffInSeconds($os)/60);
        }
      }else{
        $evt=DB::table('events')->where('id',$att->event_id)->first();
        if($evt && $evt->starts_at && $evt->ends_at){
          $S=Carbon::parse($evt->starts_at); $E=Carbon::parse($evt->ends_at);
          $os=$in->max($S); $oe=$out->min($E); if($oe->gt($os)) $sum=(int)floor($oe->diffInSeconds($os)/60);
        } else { $sum=$raw; }
      }
      $aw=$sum;
    }
    if($round>1){ $aw=(int)(round($aw/$round)*$round); }
    if($aw<$minOk) $aw=0;
    DB::table('attendances')->where('id',$att->id)->update(['check_out_at'=>$now,'minutes_raw'=>$raw,'minutes_awarded'=>$aw,'updated_at'=>$now]);
    if($aw>0){ DB::table('hours')->insert([
      'user_id'=>$att->user_id,'opportunity_id'=>$att->opportunity_id,'minutes'=>$aw,'awarded_at'=>$now,
      'attendance_id'=>$att->id,'meta'=>json_encode(['clip_to_shift'=>true,'round_to_min'=>$round]),'created_at'=>$now,'updated_at'=>$now,
    ]); }
    return response()->json(['ok'=>true,'minutes_raw'=>$raw,'minutes_awarded'=>$aw]);
  }
}
