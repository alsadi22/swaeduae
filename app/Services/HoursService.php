<?php

namespace App\Services;

use Carbon\Carbon;

class HoursService
{
    /**
     * Compute proposed minutes with rounding, clipping and auto-break.
     */
    public function proposeMinutes(Carbon $checkin, Carbon $checkout, array $opts = []): int
    {
        $minutes = max(0, $checkout->diffInMinutes($checkin));

        // Clip to shift window if provided
        if (!empty($opts['clip_to_shift']) && !empty($opts['shift_start']) && !empty($opts['shift_end'])) {
            $start = Carbon::parse($opts['shift_start']);
            $end   = Carbon::parse($opts['shift_end']);
            if ($checkin->lt($start))  $checkin = $start;
            if ($checkout->gt($end))   $checkout = $end;
            $minutes = max(0, $checkout->diffInMinutes($checkin));
        }

        // Auto break
        $autoBreak = (int)($opts['auto_break_min'] ?? 0);
        if ($autoBreak > 0 && $minutes >= max(60, $autoBreak)) {
            $minutes = max(0, $minutes - $autoBreak);
        }

        // Minimum eligible
        $minEligible = (int)($opts['min_eligible_min'] ?? 15);
        if ($minutes < $minEligible) return 0;

        // Round to grid (e.g., 5 minutes)
        $grid = max(1, (int)($opts['round_to_min'] ?? 5));
        $minutes = (int) (round($minutes / $grid) * $grid);

        return $minutes;
    }
}
