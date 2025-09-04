<?php

namespace App\Services;

class AnomalyService
{
    /** Returns [confidenceScore(0-100), flags[]] based on simple rules for now */
    public function score(float $distanceMeters = null, bool $insideWindow = true, bool $deviceReused = false): array
    {
        $score = 100;
        $flags = [];

        if ($distanceMeters !== null) {
            if ($distanceMeters > 300) { $score -= 30; $flags[] = 'gps_far'; }
            elseif ($distanceMeters > 150) { $score -= 15; $flags[] = 'gps_borderline'; }
        }
        if (!$insideWindow) { $score -= 25; $flags[] = 'outside_time_window'; }
        if ($deviceReused) { $score -= 15; $flags[] = 'device_reused'; }

        return [max(0,$score), $flags];
    }
}
