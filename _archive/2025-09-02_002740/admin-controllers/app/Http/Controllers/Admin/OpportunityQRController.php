<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OpportunityQRController extends Controller
{
    private function ensureTokens($opp){
        $changes=[];
        if (empty($opp->checkin_token))  $changes['checkin_token']=Str::random(32);
        if (empty($opp->checkout_token)) $changes['checkout_token']=Str::random(32);
        if ($changes){
            DB::table('opportunities')->where('id',$opp->id)->update($changes+['updated_at'=>now()]);
            $opp = DB::table('opportunities')->where('id',$opp->id)->first();
        }
        return $opp;
    }

    public function show($id){
        $opp = DB::table('opportunities')->where('id',$id)->first(); abort_unless($opp,404);
        $opp = $this->ensureTokens($opp);
        $checkinUrl  = url('/op/checkin/'.$opp->checkin_token);
        $checkoutUrl = url('/op/checkout/'.$opp->checkout_token);
        return view('admin.opportunities.qr', compact('opp','checkinUrl','checkoutUrl'));
    }

    public function reset($id){
        $exists = DB::table('opportunities')->where('id',$id)->exists(); abort_unless($exists,404);
        DB::table('opportunities')->where('id',$id)->update([
            'checkin_token'=>Str::random(32),
            'checkout_token'=>Str::random(32),
            'updated_at'=>now(),
        ]);
        return redirect()->route('admin.opportunities.qr',$id)->with('status', __('Tokens regenerated'));
    }

    public function finalize($id){
        $n = \App\Services\HoursService::finalize((int)$id);
        return redirect()->route('admin.opportunities.qr',$id)->with('status', __("Finalized :n records", ['n'=>$n]));
    }

    public function issue($id){
        $rows = DB::table('volunteer_hours')->where(['opportunity_id'=>$id,'locked'=>true])->get()->groupBy('user_id');
        $issued=0;
        foreach ($rows as $uid=>$group){
            $hours = round($group->sum('minutes')/60,2);
            if ($hours<=0) continue;
            $exists = DB::table('certificates')->where(['user_id'=>$uid,'opportunity_id'=>$id])->exists();
            if ($exists) continue;
            DB::table('certificates')->insert([
                'user_id'=>$uid,'opportunity_id'=>$id,'hours'=>$hours,
                'code'=>'C'.strtoupper(Str::random(8)),'uuid'=>Str::uuid(),
                'created_at'=>now(),'updated_at'=>now(),
            ]);
            $issued++;
        }
        return redirect()->route('admin.opportunities.qr',$id)->with('status', __("Issued :n certificates", ['n'=>$issued]));
    }
}
