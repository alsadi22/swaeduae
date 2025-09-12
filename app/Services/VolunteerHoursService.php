<?php

namespace App\Services;

use App\Models\VolunteerHour;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VolunteerHoursService
{
    /** Column pairs we know how to read for durations */
    protected array $timestampPairs = [
        ['checkin_at', 'checkout_at'],
        ['check_in_at', 'check_out_at'],
        ['checked_in_at', 'checked_out_at'],
    ];

    /** Return driver name: mysql|pgsql|sqlite|... */
    protected function driver(): string
    {
        return Schema::getConnection()->getDriverName();
    }

    /** Filter list to only the pairs that really exist in the DB */
    protected function availablePairs(): array
    {
        $avail = [];
        foreach ($this->timestampPairs as [$in, $out]) {
            if (Schema::hasColumn('attendances', $in) && Schema::hasColumn('attendances', $out)) {
                $avail[] = [$in, $out];
            }
        }
        return $avail;
    }

    /** Build a driver-specific SQL expression for minutes between two columns, clamped to [0, 960] */
    protected function minutesExpr(string $in, string $out): string
    {
        $driver = $this->driver();

        if ($driver === 'mysql') {
            return "GREATEST(LEAST(TIMESTAMPDIFF(MINUTE, `{$in}`, `{$out}`), 960), 0)";
        } elseif ($driver === 'pgsql') {
            return "CASE
                        WHEN EXTRACT(EPOCH FROM ({$out} - {$in}))/60 < 0 THEN 0
                        WHEN EXTRACT(EPOCH FROM ({$out} - {$in}))/60 > 960 THEN 960
                        ELSE FLOOR(EXTRACT(EPOCH FROM ({$out} - {$in}))/60)
                    END";
        } else { // sqlite & others
            return "CASE
                        WHEN ((JULIANDAY({$out}) - JULIANDAY({$in})) * 1440) < 0 THEN 0
                        WHEN ((JULIANDAY({$out}) - JULIANDAY({$in})) * 1440) > 960 THEN 960
                        ELSE CAST(((JULIANDAY({$out}) - JULIANDAY({$in})) * 1440) AS INTEGER)
                    END";
        }
    }

    /** Recalculate and store the total minutes for a single (user, opportunity) */
    public function recalcPair(int $userId, int $opportunityId): int
    {
        $total = $this->totalForPair($userId, $opportunityId);

        VolunteerHour::updateOrCreate(
            ['user_id' => $userId, 'opportunity_id' => $opportunityId],
            ['minutes' => $total]
        );

        return $total;
    }

    /** Compute (do not store) total minutes for a single (user, opportunity) */
    public function totalForPair(int $userId, int $opportunityId): int
    {
        $available = $this->availablePairs();
        $sum = 0;

        // Sum each available timestamp pair
        foreach ($available as [$in, $out]) {
            $expr = $this->minutesExpr($in, $out);
            $rowTotal = DB::table('attendances')
                ->where('user_id', $userId)
                ->where('opportunity_id', $opportunityId)
                ->whereNotNull($in)
                ->whereNotNull($out)
                ->selectRaw("SUM({$expr}) as total")
                ->value('total');

            $sum += (int) ($rowTotal ?? 0);
        }

        // Sum minutes-only rows (if column exists) where all known timestamp columns are NULL
        $hasMinutes = Schema::hasColumn('attendances', 'minutes');
        if ($hasMinutes) {
            $q = DB::table('attendances')
                ->where('user_id', $userId)
                ->where('opportunity_id', $opportunityId)
                ->selectRaw('SUM(COALESCE(minutes,0)) as total');

            foreach ($available as [$in, $out]) {
                $q->whereNull($in)->whereNull($out);
            }

            $rowTotal = $q->value('total');
            $sum += (int) ($rowTotal ?? 0);
        }

        return (int) $sum;
    }

    /** Recalculate and store totals for all user/opportunity pairs. Returns number of pairs touched. */
    public function reconcileAll(): int
    {
        $available = $this->availablePairs();
        $driver = $this->driver();

        // helper to accumulate totals
        $totals = [];
        $add = function ($uid, $oid, $mins) use (&$totals) {
            $key = $uid . ':' . $oid;
            if (!isset($totals[$key])) {
                $totals[$key] = ['user_id' => (int) $uid, 'opportunity_id' => (int) $oid, 'minutes' => 0];
            }
            $totals[$key]['minutes'] += (int) $mins;
        };

        // Aggregate per timestamp pair
        foreach ($available as [$in, $out]) {
            $expr = $this->minutesExpr($in, $out);

            $rows = DB::table('attendances')
                ->selectRaw("user_id, opportunity_id, SUM({$expr}) as total")
                ->whereNotNull('user_id')
                ->whereNotNull('opportunity_id')
                ->whereNotNull($in)
                ->whereNotNull($out)
                ->groupBy('user_id', 'opportunity_id')
                ->get();

            foreach ($rows as $r) {
                $add($r->user_id, $r->opportunity_id, $r->total);
            }
        }

        // Aggregate minutes-only rows where ALL known timestamp columns are NULL
        if (Schema::hasColumn('attendances', 'minutes')) {
            $q = DB::table('attendances')
                ->selectRaw('user_id, opportunity_id, SUM(COALESCE(minutes,0)) as total')
                ->whereNotNull('user_id')
                ->whereNotNull('opportunity_id');

            foreach ($available as [$in, $out]) {
                $q->whereNull($in)->whereNull($out);
            }

            $rows2 = $q->groupBy('user_id', 'opportunity_id')->get();
            foreach ($rows2 as $r) {
                $add($r->user_id, $r->opportunity_id, $r->total);
            }
        }

        // Persist
        $count = 0;
        foreach ($totals as $t) {
            VolunteerHour::updateOrCreate(
                ['user_id' => $t['user_id'], 'opportunity_id' => $t['opportunity_id']],
                ['minutes' => $t['minutes']]
            );
            $count++;
        }

        return $count;
    }
}
