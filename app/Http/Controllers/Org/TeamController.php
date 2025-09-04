<?php
namespace App\Http\Controllers\Org;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TeamController extends Controller
{
    public function index()
    {
        $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
        $members = DB::table('organization_users as ou')->join('users as u','u.id','=','ou.user_id')
                    ->where('ou.organization_id',$orgId)->select('ou.id','u.id as user_id','u.name','u.email','ou.role','ou.created_at')->get();
        return view('org.team.index', compact('members'));
    }
    public function invite(Request $req)
    {
        $data = $req->validate(['email'=>['required','email'],'role'=>['required','in:org_manager']]);
        $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
        $user = DB::table('users')->where('email',$data['email'])->first();
        if (!$user) {
            // Auto create minimal user
            $id = DB::table('users')->insertGetId([
                'name' => $data['email'], 'email'=>$data['email'], 'password'=>bcrypt(str()->random(16)),
                'created_at'=>now(),'updated_at'=>now()
            ]);
            $user = DB::table('users')->where('id',$id)->first();
        }
        DB::table('organization_users')->updateOrInsert(
            ['organization_id'=>$orgId,'user_id'=>$user->id],
            ['role'=>$data['role'],'updated_at'=>now(),'created_at'=>now()]
        );
        try { Mail::raw(__('You have been added to the organization team.'), function($m) use($user){ $m->to($user->email)->subject(__('Team invite')); }); } catch (\Throwable $e) {}
        return back()->with('status',__('Invited/added'));
    }
    public function remove(int $user)
    {
        $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
        DB::table('organization_users')->where(['organization_id'=>$orgId,'user_id'=>$user])->delete();
        return back()->with('status',__('Removed'));
    }
}
