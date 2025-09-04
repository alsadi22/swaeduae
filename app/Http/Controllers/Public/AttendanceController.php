<?php

namespace App\Http\Controllers\Public;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttendanceController
{
    // GET /a/{token}  (requires auth)
    public function handle(Request $request, string $token)
    {
        $opp = DB::table('opportunities')
            ->where(function($q) use ($token){
                $q->where('checkin_token',$token)->orWhere('checkout_token',$token);
            })->first();
        abort_if(!$opp, 404);

        $userId = $request->user()->id;
        $isCheckin  = $opp->checkin_token  === $token;
        $isCheckout = $opp->checkout_token === $token;

        $row = DB::table('attendances')->where(['user_id'=>$userId,'opportunity_id'=>$opp->id])->first();

        if ($isCheckin) {
            if ($row) {
                // already exists; update checkin if empty
                if (empty($row->checkin_at)) {
                    DB::table('attendances')->where('id',$row->id)->update([
                        'checkin_at'=>now(),'status'=>'checked_in','source'=>'qr','updated_at'=>now()
                    ]);
                }
            } else {
                DB::table('attendances')->insert([
                    'user_id'=>$userId,'opportunity_id'=>$opp->id,
                    'checkin_at'=>now(),'status'=>'checked_in','source'=>'qr',
                    'created_at'=>now(),'updated_at'=>now(),
                ]);
            }
            return redirect()->back()->with('status', __('Checked in successfully'));
        }

        if ($isCheckout) {
            if ($row && !empty($row->checkin_at)) {
                DB::table('attendances')->where('id',$row->id)->update([
                    'checkout_at'=>now(),'status'=>'checked_out','updated_at'=>now()
                ]);
                return redirect()->back()->with('status', __('Checked out successfully'));
            }
            // if no checkin found, create flagged row
            DB::table('attendances')->insert([
                'user_id'=>$userId,'opportunity_id'=>$opp->id,
                'checkout_at'=>now(),'status'=>'flagged','source'=>'qr',
                'created_at'=>now(),'updated_at'=>now(),
            ]);
            return redirect()->back()->with('status', __('Checkout recorded (flagged: no prior check-in)'));
        }

        abort(400);
    }
}
