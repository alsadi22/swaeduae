<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

Route::middleware(['web','auth'])->group(function () {

    // Scan page (captures geolocation; posts to same endpoint)
    Route::get('/scan', function (Request $r) {
        $code = trim($r->query('code',''));
        $op   = trim($r->query('op',''));
        return view(
            view()->exists('public.scan') ? 'public.scan' : 'opportunities.public.scan',
            compact('code','op')
        );
    })->name('scan');

    // Submit checkin/checkout
    Route::post('/scan', function (Request $r) {
        $user = $r->user();
        abort_unless($user, 403);

        $action = strtolower($r->input('action','checkin')); // checkin|checkout
        if (!in_array($action, ['checkin','checkout'])) $action = 'checkin';

        $code = trim((string)$r->input('code',''));
        $opIn = trim((string)$r->input('op',''));

        // ---- Resolve opportunity_id safely (supports different schemas) ----
        $oppId = null;
        if ($opIn !== '' && ctype_digit($opIn)) {
            $oppId = (int)$opIn;
        } elseif (Schema::hasTable('opportunities')) {
            $q = DB::table('opportunities');

            // try by id inside code (e.g., "OP-123")
            if ($oppId === null && preg_match('/(\d{1,10})/', $code, $m)) {
                $cand = (int)$m[1];
                $exists = DB::table('opportunities')->where('id',$cand)->exists();
                if ($exists) $oppId = $cand;
            }

            // try qr_code column if present
            if ($oppId === null && $code !== '' && Schema::hasColumn('opportunities','qr_code')) {
                $row = DB::table('opportunities')->where('qr_code',$code)->select('id')->first();
                if ($row) $oppId = (int)$row->id;
            }

            // try generic "code" column if present
            if ($oppId === null && $code !== '' && Schema::hasColumn('opportunities','code')) {
                $row = DB::table('opportunities')->where('code',$code)->select('id')->first();
                if ($row) $oppId = (int)$row->id;
            }
        }

        // geo/ip
        $lat = $r->input('lat');  $lng = $r->input('lng');
        $ip  = $r->ip();

        // record scan
        if (Schema::hasTable('qr_scans')) {
            DB::table('qr_scans')->insert([
                'user_id'        => $user->id,
                'opportunity_id' => $oppId,
                'action'         => $action,
                'code'           => ($code !== '' ? $code : null),
                'lat'            => is_null($lat) ? null : (float)$lat,
                'lng'            => is_null($lng) ? null : (float)$lng,
                'ip'             => $ip,
                'scanned_at'     => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // mirror geo logs
        if (Schema::hasTable('geo_logs')) {
            DB::table('geo_logs')->insert([
                'user_id' => $user->id,
                'context' => $action,
                'lat'     => is_null($lat) ? null : (float)$lat,
                'lng'     => is_null($lng) ? null : (float)$lng,
                'meta'    => json_encode(['code'=>$code,'opportunity_id'=>$oppId,'ip'=>$ip]),
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);
        }

        // compute minutes on checkout → volunteer_hours
        if ($action === 'checkout' && $oppId && Schema::hasTable('qr_scans')) {
            $checkin = DB::table('qr_scans')
                ->where('user_id', $user->id)
                ->where('opportunity_id', $oppId)
                ->where('action','checkin')
                ->orderByDesc('scanned_at')
                ->first();

            if ($checkin) {
                // ensure there is no later checkout after that checkin
                $hasLaterOut = DB::table('qr_scans')
                    ->where('user_id', $user->id)
                    ->where('opportunity_id', $oppId)
                    ->where('action','checkout')
                    ->where('scanned_at','>', $checkin->scanned_at)
                    ->exists();

                if (!$hasLaterOut) {
                    $minutes = now()->diffInMinutes(\Carbon\Carbon::parse($checkin->scanned_at));
                    $hours   = round($minutes / 60, 2);

                    if ($hours > 0 && Schema::hasTable('volunteer_hours')) {
                        DB::table('volunteer_hours')->insert([
                            'user_id'        => $user->id,
                            'opportunity_id' => $oppId,
                            'hours'          => $hours,
                            'note'           => 'QR session '.($code ?: '#'.$oppId).' ('.$minutes.' min)',
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);
                    }
                }
            }
        }

        return back()->with('status', ucfirst($action).' recorded.'.($oppId ? " Opportunity #$oppId." : ''));
    })->name('scan.submit');
});
