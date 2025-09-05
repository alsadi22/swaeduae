<?php
namespace App\Http\Controllers\Org;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KycController extends Controller
{
    public function edit()
    {
        $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
        $kyc = DB::table('org_kyc')->where('organization_id',$orgId)->first();
        return view('org.kyc.edit', compact('kyc'));
    }
    public function update(Request $req)
    {
        $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
        $data = $req->validate(['license'=>['required','file','mimes:pdf,jpg,jpeg,png','max:8192']]);
        $path = $req->file('license')->store('uploads/kyc','public');
        DB::table('org_kyc')->updateOrInsert(
          ['organization_id'=>$orgId],
          ['status'=>'pending','file_path'=>"/storage/$path",'submitted_at'=>now(),'updated_at'=>now(),'created_at'=>now()]
        );
        return back()->with('status',__('Submitted for review'));
    }
}
