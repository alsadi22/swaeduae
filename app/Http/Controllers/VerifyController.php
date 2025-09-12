<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class VerifyController extends Controller
{
    public function show(string $code)
    {
        $c = DB::table('certificates')->where('code', $code)->orWhere('verification_code',$code)->first();
        if (!$c) return view('verify.result', ['ok'=>false, 'code'=>$code]);

        $user = DB::table('users')->where('id',$c->user_id)->first();
        $opp  = DB::table('opportunities')->where('id',$c->opportunity_id)->first();

        return view('verify.result', [
            'ok'   => true,
            'code' => $code,
            'name' => $user->name ?? ('#'.$c->user_id),
            'opportunity' => $opp->title ?? ('#'.$c->opportunity_id),
            'hours'=> $c->hours ?? null,
            'file' => $c->file_path ?? null,
        ]);
    }
}
