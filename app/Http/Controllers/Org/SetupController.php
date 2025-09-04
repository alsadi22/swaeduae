<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetupController extends Controller
{
    public function form()
    {
        return view('org.setup');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:191',
            'license_no'=>'nullable|string|max:191',
        ]);

        $u = Auth::user();

        if (($u->role ?? 'user') !== 'org') {
            DB::table('users')->where('id',$u->id)->update(['role'=>'org','updated_at'=>now()]);
        }

        DB::table('organizations')->updateOrInsert(
            ['owner_user_id'=>$u->id],
            [
                'name'=>$request->name,
                'license_no'=>$request->license_no,
                'status'=>'active',
                'updated_at'=>now(),
                'created_at'=>now(),
            ]
        );

        return redirect()->route('org.dashboard')->with('status', __('swaed.org_created') ?? 'Organization saved.');
    }
}
