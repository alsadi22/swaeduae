<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\VolunteerHour;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Compute and persist volunteer hours from a completed attendance record.
     * Idempotent: re-calculates if changed.
     */
    public function computeHoursFromAttendance(Attendance $attendance): ?VolunteerHour
    {
        if (!$attendance->check_in_at || !$attendance->check_out_at) {
            return null;
        }

        $userId = $attendance->user_id ?? $attendance->volunteer_id ?? null;
        $eventId = $attendance->event_id ?? $attendance->opportunity_id ?? null;

        if (!$userId || !$eventId) {
            return null;
        }

        $seconds = max(0, strtotime($attendance->check_out_at) - strtotime($attendance->check_in_at));
        $hours = round($seconds / 3600, 2);

        return DB::transaction(function () use ($attendance, $userId, $eventId, $hours) {
            // Upsert a VolunteerHour row for (user,event,attendance)
            $vh = VolunteerHour::query()
                ->firstOrNew([
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'attendance_id' => $attendance->id,
                ]);

            $vh->hours = $hours;
            $vh->source = $vh->source ?: 'attendance';
            $vh->save();

            return $vh;
        });
    }

    /**
     * Get a user's total hours (optionally filtered by event).
     */
    public function totalHours(int $userId, ?int $eventId = null): float
    {
        $q = VolunteerHour::query()->where('user_id', $userId);
        if ($eventId) $q->where('event_id', $eventId);
        return (float) $q->sum('hours');
    }
}
