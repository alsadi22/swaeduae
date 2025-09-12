<?php
namespace App\Http\Controllers\My;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApplicationsController extends Controller {
  public function index() {
    $uid = auth()->id();
    $rows = DB::table('applications as a')
      ->join('opportunities as o','o.id','=','a.opportunity_id')
      ->where('a.user_id',$uid)
      ->orderByDesc('a.created_at')
      ->select('a.id','a.status','a.created_at','o.title','o.location','o.starts_at','o.ends_at')
      ->paginate(20);
    return view('my.applications', compact('rows'));
  }
}
