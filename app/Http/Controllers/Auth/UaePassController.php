<?php
namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;

class UaePassController
{
    public function redirect(Request $r) {
        if (!config('app.uae_pass_enabled', false)) return response('UAE PASS disabled', 403);
        return response('UAE PASS redirect (sandbox stub)', 501);
    }
    public function callback(Request $r) {
        return response('UAE PASS callback (sandbox stub)', 501);
    }
}
