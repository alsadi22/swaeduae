<?php
namespace App\Http\Controllers\QR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifyController extends Controller
{
    public function show(Request $r, $serial = null)
    {
        $code = trim($serial ?: (string)$r->query('code', ''));
        $certificate = null;
        $valid = false;
        $volunteer_name = $event_title = null;
        $hours = null;

        if ($code !== '') {
            $certificate = DB::table('certificates')->where('code', $code)->first();
            if ($certificate && is_null($certificate->revoked_at)) {
                $valid = true;
                // try to enrich fields (optional)
                $user = DB::table('users')->where('id', $certificate->user_id)->first();
                $event = DB::table('events')->where('id', $certificate->event_id)->first();
                $volunteer_name = $user->name ?? null;
                $event_title = $event->title ?? null;
                $hours = $certificate->hours ?? null;
            }
        }

        return view('qr.verify', compact('certificate','serial','valid','volunteer_name','event_title','hours'));
    }
}
