<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Services\QrTokenService;
use App\Services\HoursService;
use App\Services\AnomalyService;

class ScanController extends Controller
{
    /** GET shows confirm page (grabs geolocation & posts); POST performs checkin/checkout */
    public function scan(Request $request, QrTokenService $qr, HoursService $hours, AnomalyService $anomaly)
    {
        // Enforce auth: volunteers must be logged in to bind scan to their user
        if (!Auth::check()) {
            // Preserve token in session and redirect to login
            return redirect()->guest(route('login').'?redirect='.url()->current().'%3F'.http_build_query(['t'=>$request->get('t')]));
        }

        $token = (string) $request->get('t');
        if (!$token) {
            return view('scan.result', ['status' => 'error', 'message' => 'Missing token.']);
        }

        // Decrypt token
        try {
            $payload = json_decode(Crypt::decryptString(base64_decode($token)), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return view('scan.result', ['status' => 'error', 'message' => 'Invalid token.']);
        }

        $opportunityId = (int)($payload['op'] ?? 0);
        $direction     = ($payload['dir'] ?? 'in') === 'out' ? 'out' : 'in';
        $issuedAt      = (int)($payload['ts'] ?? 0);

        // Check token freshness
        $ttl = (int) Config::get('hours.token_ttl_seconds', 120);
        if (!$qr->isFresh($issuedAt, $ttl)) {
            return view('scan.result', ['status' => 'expired', 'message' => 'QR expired. Please rescan the latest code.']);
        }

        $userId = Auth::id();

        // GET -> show confirmation page that captures geolocation and POSTS back
        if ($request->method() === 'GET') {
            return view('scan.confirm', compact('token','direction','opportunityId','ttl'));
        }

        // POST -> finalize checkin/checkout
        $now = Carbon::now();

        // Optional geolocation
        $lat = $request->float('lat', null);
        $lng = $request->float('lng', null);

        // Device hash (simple UA hash)
        $ua = substr(sha1($request->userAgent() ?? ''), 0, 40);

        // Geofence & time window checks (best-effort, schema-flexible)
        $distance = null;
        $insideWindow = true;
        $clipToShift = (bool) Config::get('hours.clip_to_shift', true);
        $autoBreak   = (int) Config::get('hours.auto_break_min', 0);
        $roundTo     = (int) Config::get('hours.round_to_min', 5);
        $minEligible = (int) Config::get('hours.min_eligible_min', 15);

        $oppLat = $oppLng = null;
        $geofenceMeters = (int) Config::get('hours.geofence_meters', 150);

        if (Schema::hasTable('opportunities')) {
            $o = DB::table('opportunities')->where('id', $opportunityId)->first();

            if ($o) {
                // Try common datetime columns to gate time window
                $start = $o->start_date ?? $o->starts_at ?? $o->start_at ?? null;
                $end   = $o->end_date   ?? $o->ends_at   ?? $o->end_at   ?? null;
                try {
                    if ($start && $end) {
                        $insideWindow = $now->between(Carbon::parse($start)->subMinutes(30), Carbon::parse($end)->addMinutes(120));
                    }
                } catch (\Throwable $e) { $insideWindow = true; }

                // Try common geolocation columns
                $oppLat = $o->lat ?? $o->latitude ?? null;
                $oppLng = $o->lng ?? $o->longitude ?? null;

                if ($oppLat !== null && $oppLng !== null && $lat !== null && $lng !== null) {
                    $distance = $this->haversine((float)$oppLat, (float)$oppLng, (float)$lat, (float)$lng);
                    if ($distance > $geofenceMeters) {
                        // Still allow check, but it will be flagged low-confidence
                    }
                }
            }
        }

        // Upsert attendance
        if (!Schema::hasTable('attendances')) {
            return view('scan.result', ['status'=>'error','message'=>'Attendance table missing.']);
        }

        $att = DB::table('attendances')->where([
            'user_id' => $userId,
            'opportunity_id' => $opportunityId
        ])->orderByDesc('id')->first();

        if ($direction === 'in') {
            if (!$att || empty($att->checkin_at) || !empty($att->checkout_at)) {
                DB::table('attendances')->insert([
                    'user_id' => $userId,
                    'opportunity_id' => $opportunityId,
                    'checkin_at' => $now,
                    'checkout_at' => null,
                    'method' => 'qr',
                    'lat' => $lat, 'lng' => $lng, 'device_hash' => $ua,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            } // else already checked in; ignore duplicate
            return view('scan.result', ['status'=>'in','message'=>'Checked in successfully.']);
        }

        // direction === 'out'
        if ($att && !empty($att->checkin_at) && empty($att->checkout_at)) {
            DB::table('attendances')->where('id', $att->id)->update([
                'checkout_at' => $now,
                'updated_at' => $now,
                'method' => 'qr',
                'lat' => $lat ?? $att->lat, 'lng' => $lng ?? $att->lng, 'device_hash' => $ua ?: $att->device_hash,
            ]);

            // Propose minutes → volunteer_hours (pending)
            if (Schema::hasTable('volunteer_hours')) {
                $minutes = app(HoursService::class)->proposeMinutes(
                    Carbon::parse($att->checkin_at),
                    $now,
                    [
                        'clip_to_shift'   => $clipToShift,
                        'shift_start'     => $start ?? null,
                        'shift_end'       => $end ?? null,
                        'auto_break_min'  => $autoBreak,
                        'round_to_min'    => $roundTo,
                        'min_eligible_min'=> $minEligible,
                    ]
                );

                // Score & flags
                [$score, $flags] = app(AnomalyService::class)->score($distance, $insideWindow, false);

                if ($minutes > 0) {
                    // upsert pending hours
                    $existing = DB::table('volunteer_hours')
                        ->where(['user_id'=>$userId, 'opportunity_id'=>$opportunityId])
                        ->orderByDesc('id')->first();

                    if ($existing) {
                        DB::table('volunteer_hours')->where('id',$existing->id)->update([
                            'minutes' => $minutes,
                            'status'  => 'pending',
                            'confidence_score' => $score,
                            'anomaly_flags'    => empty($flags) ? null : json_encode($flags),
                            'updated_at' => $now,
                        ]);
                    } else {
                        DB::table('volunteer_hours')->insert([
                            'user_id' => $userId,
                            'opportunity_id' => $opportunityId,
                            'minutes' => $minutes,
                            'status'  => 'pending',
                            'confidence_score' => $score,
                            'anomaly_flags'    => empty($flags) ? null : json_encode($flags),
                            'created_at' => $now, 'updated_at' => $now,
                        ]);
                    }
                }
            }

            return view('scan.result', ['status'=>'out','message'=>'Checked out. Hours submitted for approval.']);
        }

        return view('scan.result', ['status'=>'error','message'=>'No active check-in found for checkout.']);
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
}
