<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KycController extends Controller
{
    public function index()
    {
        $rows = DB::table('org_kyc as k')->join('organizations as o','o.id','=','k.organization_id')
                 ->select('k.*','o.name as org_name')->orderByDesc('k.updated_at')->paginate(30);
        return view('admin.kyc.index', compact('rows'));
    }
    public function approve(int $org, Request $req)
    {
        DB::table('org_kyc')->where('organization_id',$org)->update([
          'status'=>'approved','reviewed_at'=>now(),'reviewed_by'=>Auth::id(),'review_note'=>$req->input('note'),'updated_at'=>now()
        ]);
        return back()->with('status','Approved');
    }
    public function decline(int $org, Request $req)
    {
        DB::table('org_kyc')->where('organization_id',$org)->update([
          'status'=>'declined','reviewed_at'=>now(),'reviewed_by'=>Auth::id(),'review_note'=>$req->input('note'),'updated_at'=>now()
        ]);
        return back()->with('status','Declined');
    }
}
