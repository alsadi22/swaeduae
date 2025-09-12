<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
  public function apply(Request $r, $id){
    $r->validate(['note'=>'nullable|string|max:1000']);
    $uid = $r->user()->id;
    $exists = DB::table('opportunities')->where('id',$id)->exists(); abort_unless($exists,404);
    DB::table('applications')->updateOrInsert(
      ['user_id'=>$uid,'opportunity_id'=>$id],
      ['status'=>'pending','note'=>$r->note,'updated_at'=>now(),'created_at'=>now()]
    );
    return back()->with('status', __('Applied successfully'));
  }

  public function cancel(Request $r, $id){
    DB::table('applications')->where(['user_id'=>$r->user()->id,'opportunity_id'=>$id])->update(['status'=>'cancelled','updated_at'=>now()]);
    return back()->with('status', __('Application cancelled'));
  }
}
