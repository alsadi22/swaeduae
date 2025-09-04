<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\HoursService;
use App\Services\CertificateService;

class AttendanceAdminController extends Controller
{
    public function qr(Request $request, int $opportunityId)
    {
        $opp = DB::table('opportunities')->where('id',$opportunityId)->first();
        abort_unless($opp,404);

        // generate tokens if missing
        $updates = [];
        if (empty($opp->checkin_token))  $updates['checkin_token']  = Str::random(40);
        if (empty($opp->checkout_token)) $updates['checkout_token'] = Str::random(40);
        if ($updates) {
            DB::table('opportunities')->where('id',$opportunityId)->update($updates);
            $opp = DB::table('opportunities')->where('id',$opportunityId)->first();
        }

        $checkinUrl  = url('/a/'.$opp->checkin_token);
        $checkoutUrl = url('/a/'.$opp->checkout_token);

        return view('admin.attendance.qr', compact('opp','checkinUrl','checkoutUrl'));
    }

    public function index(Request $request)
    {
        $oid = (int) $request->get('opportunity_id', 0);
        $rows = DB::table('attendances as a')
            ->join('users as u','u.id','=','a.user_id')
            ->when($oid>0, fn($x)=>$x->where('a.opportunity_id',$oid))
            ->orderByDesc('a.id')
            ->select('a.*','u.name','u.volunteer_code')
            ->paginate(30)->withQueryString();

        return view('admin.attendance.index', compact('rows','oid'));
    }

    public function manual(Request $request, int $opportunityId)
    {
        $request->validate([
            'user_id'=>'required|integer',
            'action' =>'required|in:checkin,checkout',
        ]);
        $u = (int)$request->user_id;
        $row = DB::table('attendances')->where(['user_id'=>$u,'opportunity_id'=>$opportunityId])->first();
        if ($request->action==='checkin') {
            if ($row) {
                if (empty($row->checkin_at)) {
                    DB::table('attendances')->where('id',$row->id)->update([
                        'checkin_at'=>now(),'status'=>'checked_in','source'=>'manual','updated_at'=>now()
                    ]);
                }
            } else {
                DB::table('attendances')->insert([
                    'user_id'=>$u,'opportunity_id'=>$opportunityId,
                    'checkin_at'=>now(),'status'=>'checked_in','source'=>'manual',
                    'created_at'=>now(),'updated_at'=>now(),
                ]);
            }
        } else { // checkout
            if ($row && !empty($row->checkin_at)) {
                DB::table('attendances')->where('id',$row->id)->update([
                    'checkout_at'=>now(),'status'=>'checked_out','updated_at'=>now()
                ]);
            } else {
                DB::table('attendances')->insert([
                    'user_id'=>$u,'opportunity_id'=>$opportunityId,
                    'checkout_at'=>now(),'status'=>'flagged','source'=>'manual',
                    'created_at'=>now(),'updated_at'=>now(),
                ]);
            }
        }
        return back()->with('status', __('Attendance updated'));
    }

    public function finalize(Request $request, int $opportunityId)
    {
        $n = HoursService::finalize($opportunityId);
        return back()->with('status', __('Finalized hours for :n records',['n'=>$n]));
    }

    public function complete(Request $request, int $opportunityId)
    {
        // mark completed
        DB::table('opportunities')->where('id',$opportunityId)->update([
            'status'=>'completed','completed_at'=>now(),'updated_at'=>now()
        ]);

        // finalize hours and issue certificates
        $n = \App\Services\HoursService::finalize($opportunityId);
        $c = \App\Services\CertificateService::issueForOpportunity($opportunityId);

        return back()->with('status', __("Completed. Finalized hours: :n, Certificates issued: :c", ['n'=>$n,'c'=>$c]));
    }
}
