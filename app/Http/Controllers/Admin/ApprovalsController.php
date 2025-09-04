<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdminAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ApprovalsController extends Controller {
  public function index(Request $r) {
    $type   = $r->string('type')->toString() ?: 'all';
    $status = $r->string('status')->toString() ?: 'pending';
    $orgs = collect(); $apps = collect();
    if ($type==='all'||$type==='orgs') {
      $orgs = DB::table('org_profiles')->select('id','org_name','org_code','status','created_at')
        ->when($status, fn($q)=>$q->where('status',$status))->orderByDesc('created_at')->limit(50)->get();
    }
    if ($type==='all'||$type==='apps') {
      $apps = DB::table('applications')->select('id','user_id','opportunity_id','status','created_at')
        ->when($status, fn($q)=>$q->where('status',$status))->orderByDesc('created_at')->limit(50)->get();
    }
    return view('admin.approvals.index', compact('type','status','orgs','apps'));
  }
  public function approveOrg(Request $r,int $id){return $this->org($r,$id,'approved');}
  public function denyOrg(Request $r,int $id){return $this->org($r,$id,'denied');}
  public function approveApp(Request $r,int $id){return $this->app($r,$id,'approved');}
  public function denyApp(Request $r,int $id){return $this->app($r,$id,'denied');}
  protected function org(Request $r,int $id,string $new){
    $reason=(string)$r->input('reason','');
    DB::transaction(function() use($id,$new,$reason,$r){
      $row=DB::table('org_profiles')->where('id',$id)->lockForUpdate()->first(); abort_unless($row,404);
      DB::table('org_profiles')->where('id',$id)->update(['status'=>$new,'updated_at'=>now()]);
      AdminAction::create(['admin_id'=>$r->user()->id,'action'=>$new,'subject_type'=>'org','subject_id'=>$id,'meta'=>['reason'=>$reason]]);
    });
    return back()->with('status',"Organization #$id {$new}.");
  }
  protected function app(Request $r,int $id,string $new){
    $reason=(string)$r->input('reason','');
    DB::transaction(function() use($id,$new,$reason,$r){
      $row=DB::table('applications')->where('id',$id)->lockForUpdate()->first(); abort_unless($row,404);
      DB::table('applications')->where('id',$id)->update(['status'=>$new,'updated_at'=>now()]);
      AdminAction::create(['admin_id'=>$r->user()->id,'action'=>$new,'subject_type'=>'application','subject_id'=>$id,'meta'=>['reason'=>$reason]]);
    });
    return back()->with('status',"Application #$id {$new}.");
  }
}
