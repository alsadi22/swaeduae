<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/qr/verify', function (Request $request) {
    $code = $request->query('code');
    if ($code) {
        return redirect()->to(url('/qr/verify/'.rawurlencode($code)));
    }
    return response()->view('qr.verify', [], 200);
})
->middleware('throttle:30,1')  // 30 req/min/IP
->withoutMiddleware([\App\Http\Middleware\EnforceOrgRegistration::class]); // keep public
