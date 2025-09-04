<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HoursAudit extends Command
{
    protected $signature = 'hours:audit {--notify : Force-send alerts even if mismatches=0}';
    protected $description = 'Audit VolunteerHour totals vs Attendance and alert on mismatches';

    public function handle(): int
    {
        // Detect columns
        $hasCheckIn  = Schema::hasColumn('attendances', 'checkin_at')  || Schema::hasColumn('attendances', 'check_in_at');
        $hasCheckOut = Schema::hasColumn('attendances', 'checkout_at') || Schema::hasColumn('attendances', 'check_out_at');
        $hasMinutes  = Schema::hasColumn('attendances', 'minutes');

        $inCol  = Schema::hasColumn('attendances', 'checkin_at')  ? 'checkin_at'  : (Schema::hasColumn('attendances','check_in_at')  ? 'check_in_at'  : null);
        $outCol = Schema::hasColumn('attendances', 'checkout_at') ? 'checkout_at' : (Schema::hasColumn('attendances','check_out_at') ? 'check_out_at' : null);

        $expected = []; // "user_id:opp_id" => minutes
        $key = fn($u,$o) => "{$u}:{$o}";

        // 1) Timestamp rows -> per-row minutes, clamped [0, 960]
        if ($hasCheckIn && $hasCheckOut) {
            $q = DB::table('attendances')
                ->select('user_id','opportunity_id', $inCol.' as _in', $outCol.' as _out')
                ->whereNotNull('user_id')->whereNotNull('opportunity_id')
                ->whereNotNull($inCol)->whereNotNull($outCol)
                ->orderBy('user_id')->orderBy('opportunity_id');

            foreach ($q->cursor() as $r) {
                $in  = Carbon::parse($r->_in);
                $out = Carbon::parse($r->_out);
                $mins = $out->diffInMinutes($in, false);
                $mins = max(0, min(960, (int)$mins));
                $k = $key($r->user_id, $r->opportunity_id);
                $expected[$k] = ($expected[$k] ?? 0) + $mins;
            }
        }

        // 2) Minutes-only rows (no timestamps)
        if ($hasMinutes) {
            $q2 = DB::table('attendances')
                ->select('user_id','opportunity_id','minutes')
                ->whereNotNull('user_id')->whereNotNull('opportunity_id')
                ->when($hasCheckIn && $hasCheckOut, function ($q) use ($inCol, $outCol) {
                    $q->whereNull($inCol)->whereNull($outCol);
                })
                ->whereNotNull('minutes')
                ->orderBy('user_id')->orderBy('opportunity_id');

            foreach ($q2->cursor() as $r) {
                $k = $key($r->user_id, $r->opportunity_id);
                $expected[$k] = ($expected[$k] ?? 0) + (int)$r->minutes;
            }
        }

        // Load stored totals
        $stored = [];
        $q3 = DB::table('volunteer_hours')
            ->select('user_id','opportunity_id','minutes')
            ->orderBy('user_id')->orderBy('opportunity_id');

        foreach ($q3->cursor() as $r) {
            $stored[$key($r->user_id,$r->opportunity_id)] = (int)$r->minutes;
        }

        // Compare
        $mismatches = [];
        $allKeys = array_unique(array_merge(array_keys($expected), array_keys($stored)));
        sort($allKeys);
        foreach ($allKeys as $k) {
            [$u,$o] = array_map('intval', explode(':',$k));
            $e = $expected[$k] ?? 0;
            $s = $stored[$k]   ?? 0;
            if ($e !== $s) {
                $mismatches[] = ['user_id'=>$u,'opportunity_id'=>$o,'expected'=>$e,'stored'=>$s,'delta'=>$e-$s];
            }
        }

        // Output
        if (empty($mismatches)) {
            $this->info('Audit complete. mismatches=0');
        } else {
            $this->warn('Audit found '.count($mismatches).' mismatches:');
            foreach ($mismatches as $m) {
                $this->line(" - user {$m['user_id']} / opp {$m['opportunity_id']}: expected {$m['expected']} vs stored {$m['stored']} (delta {$m['delta']})");
            }
        }

        // Alerts
        $shouldNotify = $this->option('notify') || !empty($mismatches);
        if ($shouldNotify) {
            $summary = empty($mismatches)
                ? "Volunteer hours audit OK. No mismatches."
                : "Volunteer hours audit found ".count($mismatches)." mismatches:\n"
                  . collect($mismatches)->map(fn($m) =>
                      "user {$m['user_id']} / opp {$m['opportunity_id']}: expected {$m['expected']} vs stored {$m['stored']} (delta {$m['delta']})"
                    )->implode("\n");

            if ($to = env('OPS_ALERT_EMAIL')) {
                try {
                    Mail::raw($summary, function($m) use ($to, $summary){
                        $m->to($to)->subject('Hours Audit '.(str_contains($summary,'mismatch') ? '⚠️ Issues' : '✅ OK'));
                    });
                } catch (\Throwable $e) {
                    $this->error("Email alert failed: ".$e->getMessage());
                }
            }

            if ($hook = env('SLACK_WEBHOOK_URL')) {
                try {
                    Http::asJson()->post($hook, ['text' => "```\n{$summary}\n```"]);
                } catch (\Throwable $e) {
                    $this->error("Slack alert failed: ".$e->getMessage());
                }
            }
        }

        return empty($mismatches) ? self::SUCCESS : self::FAILURE;
    }
}
