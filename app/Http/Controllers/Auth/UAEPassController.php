<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UAEPassController extends Controller
{
    public function redirect()
    {
        // Require env values to be set first
        if (!config('services.uaepass.client_id') || !env('UAEPASS_DISCOVERY_URL') || !env('UAEPASS_ISSUER')) {
            return back()->with('error', 'UAEPass is not configured yet. Please add UAEPASS_ISSUER and UAEPASS_DISCOVERY_URL to .env');
        }
        // Minimal placeholder: send user to callback with a notice for now.
        // (Swap this with a full OIDC flow once the values are provided.)
        return redirect()->route('uaepass.callback')->with('error','UAEPass OIDC wiring pending configuration.');
    }

    public function callback(Request $request)
    {
        return redirect('/volunteer/login')->with('error', 'UAEPass not fully configured yet.');
    }
}
