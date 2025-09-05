<?php
namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CompletionController extends Controller
{
    public function show($id)
    {
        $opp = DB::table('opportunities')->where('id', $id)->first();
        abort_unless($opp, 404);
        return view('org.complete', compact('opp'));
    }

    public function store($id, Request $r)
    {
        $r->validate(['csv' => 'required|file|mimes:csv,txt']);

        $opp = DB::table('opportunities')->where('id', $id)->first();
        abort_unless($opp, 404);

        $round  = (int) config('hours.round_to_min', 5);
        $minOk  = (int) config('hours.min_eligible_min', 15);
        $clip   = (bool) config('hours.clip_to_shift', true);

        $rows = array_map('str_getcsv', file($r->file('csv')->getRealPath()));
        if (!$rows || count($rows) < 2) {
            return back()->withErrors(['csv' => 'CSV is empty or missing header row.']);
        }

        // header: email,event_id,check_in_at,check_out_at,minutes (minutes optional)
        $header = array_map('trim', array_shift($rows));
        $idx = array_flip($header);

        $count = 0;
        $awarded = 0;

        foreach ($rows as $row) {
            $row = array_map('trim', $row);
            $email = $row[$idx['email']] ?? null;
            if (!$email) continue;

            $user = DB::table('users')->where('email', $email)->first();
            if (!$user) continue;

            $eventId = $row[$idx['event_id']] ?? $opp->id;

            $cin  = $row[$idx['check_in_at']]  ?? null;
            $cout = $row[$idx['check_out_at']] ?? null;
            $mins = isset($idx['minutes']) && $row[$idx['minutes']] !== '' ? (int)$row[$idx['minutes']] : null;

            $cinAt  = $cin  ? Carbon::parse($cin)  : null;
            $coutAt = $cout ? Carbon::parse($cout) : null;

            if (!$mins && $cinAt && $coutAt) {
                if ($clip) {
                    if ($opp->starts_at) $cinAt  = max($cinAt,  Carbon::parse($opp->starts_at));
                    if ($opp->ends_at)   $coutAt = min($coutAt, Carbon::parse($opp->ends_at));
                }
                $mins = max(0, $coutAt->diffInMinutes($cinAt));
            }

            $minsRaw    = (int) ($mins ?? 0);
            $minsAward  = $minsRaw >= $minOk ? (int) (round($minsRaw / $round) * $round) : 0;

            // upsert attendance
            $existing = DB::table('attendances')
                ->where(['user_id' => $user->id, 'opportunity_id' => $opp->id, 'event_id' => $eventId])
                ->orderByDesc('id')->first();

            $attData = [
                'user_id'          => $user->id,
                'opportunity_id'   => $opp->id,
                'event_id'         => $eventId,
                'check_in_at'      => $cinAt ?: now(),
                'check_out_at'     => $coutAt,
                'source'           => 'admin',
                'status'           => 'ok',
                'minutes_raw'      => $minsRaw,
                'minutes_awarded'  => $minsAward,
                'meta'             => json_encode(['csv_email' => $email]),
                'updated_at'       => now(),
            ];

            if ($existing) {
                DB::table('attendances')->where('id', $existing->id)->update($attData);
                $attId = $existing->id;
            } else {
                $attData['created_at'] = now();
                $attId = DB::table('attendances')->insertGetId($attData);
            }

            // upsert hours by attendance
            $hoursExists = DB::table('hours')->where('attendance_id', $attId)->exists();
            DB::table('hours')->updateOrInsert(
                ['attendance_id' => $attId],
                [
                    'user_id'        => $user->id,
                    'opportunity_id' => $opp->id,
                    'minutes'        => $minsAward,
                    'awarded_at'     => now(),
                    'meta'           => json_encode(['source' => 'completion_csv']),
                    'updated_at'     => now(),
                ] + ($hoursExists ? [] : ['created_at' => now()])
            );

            // issue certificate once per (user, opportunity, event) when minutes > 0
            if ($minsAward > 0) {
                $existsCert = DB::table('certificates')
                    ->where(['user_id' => $user->id, 'opportunity_id' => $opp->id, 'event_id' => $eventId])
                    ->exists();

                if (!$existsCert) {
                    do {
                        $code = (config('app.name', 'SWAED')) . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
                    } while (DB::table('certificates')->where('code', $code)->exists());

                    DB::table('certificates')->insert([
                        'uuid'           => (string) Str::uuid(),
                        'user_id'        => $user->id,
                        'opportunity_id' => $opp->id,
                        'event_id'       => $eventId,
                        'code'           => $code,
                        'signature'      => (string) Str::uuid(),
                        'pdf_path'       => null,
                        'hours'          => round($minsAward / 60, 2),
                        'issued_at'      => now(),
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }

            $count++;
            $awarded += $minsAward;
        }

        return back()->with('status', "Processed {$count} rows; awarded " . round($awarded / 60, 2) . " hours.");
    }
}
