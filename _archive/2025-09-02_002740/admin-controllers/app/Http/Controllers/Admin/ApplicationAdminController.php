<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationAdminController extends Controller
{
  public function index(Request $r){
    $q = trim((string)$r->get('q',''));
    $rows = DB::table('applications')
      ->select('applications.*','users.name as user_name','opportunities.title as opp_title')
      ->leftJoin('users','users.id','=','applications.user_id')
      ->leftJoin('opportunities','opportunities.id','=','applications.opportunity_id')
      ->when($q!=='', fn($x)=>$x->where('users.name','like',"%$q%")->orWhere('opportunities.title','like',"%$q%"))
      ->orderByDesc('applications.id')->paginate(25)->withQueryString();
    return view('admin/applications/index', compact('rows','q'));
  }

  public function update(Request $r, $id){
    $r->validate(['status'=>'required|in:pending,shortlisted,approved,rejected,cancelled']);
    DB::table('applications')->where('id',$id)->update(['status'=>$r->status,'updated_at'=>now()]);
    return back()->with('status', __('Updated'));
  }
}
