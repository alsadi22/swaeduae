<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AttendanceController extends Controller
{
    private function needAuth(Request $r){ return $r->user() ? null : redirect()->guest(route('login'))->with('status', __('Please sign in to continue.')); }

    public function checkin(Request $r, string $token){
        if ($redir = $this->needAuth($r)) return $redir;
        $opp = DB::table('opportunities')->where('checkin_token',$token)->first(); abort_unless($opp,404);
        DB::table('attendances')->updateOrInsert(
            ['user_id'=>$r->user()->id,'opportunity_id'=>$opp->id],
            ['checkin_at'=>now(),'status'=>'checked_in','source'=>'qr','updated_at'=>now(),'created_at'=>now()]
        );
        return view('attendance.ok', ['mode'=>'in','opp'=>$opp]);
    }
    public function checkout(Request $r, string $token){
        if ($redir = $this->needAuth($r)) return $redir;
        $opp = DB::table('opportunities')->where('checkout_token',$token)->first(); abort_unless($opp,404);
        DB::table('attendances')->updateOrInsert(
            ['user_id'=>$r->user()->id,'opportunity_id'=>$opp->id],
            ['checkout_at'=>now(),'status'=>'checked_out','source'=>'qr','updated_at'=>now(),'created_at'=>now()]
        );
        return view('attendance.ok', ['mode'=>'out','opp'=>$opp]);
    }
}
