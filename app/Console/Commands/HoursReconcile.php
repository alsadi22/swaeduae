<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HoursReconcile extends Command
{
    protected $signature = 'hours:reconcile {--force}';
    protected $description = 'Recompute volunteer hours from attendance and sync volunteer_hours table';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        // Collect all distinct user/opportunity pairs from attendances and volunteer_hours
        $attPairs = DB::table('attendances')->select('user_id','opportunity_id')->distinct()->get();
        $vhPairs  = DB::table('volunteer_hours')->select('user_id','opportunity_id')->distinct()->get();

        $pairs = $attPairs->concat($vhPairs)
            ->filter(fn ($r) => !empty($r->user_id) && !empty($r->opportunity_id))
            ->unique(fn ($r) => $r->user_id.'-'.$r->opportunity_id)
            ->values();

        $reconciled = 0;

        foreach ($pairs as $p) {
            $u = (int) $p->user_id;
            $o = (int) $p->opportunity_id;

            // Canonical minutes calculation:
            // 1) minutes column if present
            // 2) else hours*60
            // 3) else TIMESTAMPDIFF on any of the known column variants
            $expected = (int) (DB::table('attendances')
                ->where('user_id', $u)
                ->where('opportunity_id', $o)
                ->selectRaw("
                    COALESCE(
                        SUM(
                            COALESCE(
                                minutes,
                                IFNULL(hours,0) * 60,
                                TIMESTAMPDIFF(
                                    MINUTE,
                                    COALESCE(check_in_at, checked_in_at, checkin_at),
                                    COALESCE(check_out_at, checked_out_at, checkout_at)
                                )
                            )
                        ),
                        0
                    ) as s
                ")
                ->value('s') ?? 0);

            $hours   = intdiv($expected, 60);
            $minutes = $expected;

            // Upsert into volunteer_hours with canonical values
            $existing = DB::table('volunteer_hours')
                ->where('user_id', $u)
                ->where('opportunity_id', $o)
                ->first();

            if ($existing) {
                // If you later add a finalized flag/column, gate with !$force
                DB::table('volunteer_hours')
                    ->where('id', $existing->id)
                    ->update([
                        'minutes'    => $minutes,
                        'hours'      => $hours,
                        'source'     => 'reconciled',
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('volunteer_hours')->insert([
                    'user_id'        => $u,
                    'opportunity_id' => $o,
                    'minutes'        => $minutes,
                    'hours'          => $hours,
                    'source'         => 'reconciled',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            $reconciled++;
        }

        $this->info("Reconciled {$reconciled} user/opportunity pair(s).");
        return 0;
    }
}
