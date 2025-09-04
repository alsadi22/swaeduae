<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrgMetrics
{
    /**
     * Compute dashboard metrics for an org between $from and $to (Carbon dates).
     * Returns array: total_minutes, attendance_total, present, no_show, unfinalized
     */
    public static function compute(int $orgId, Carbon $from, Carbon $to): array
    {
        // Hours (sum volunteer_hours minutes for this org in range)
        $totalMinutes = (int) DB::table('volunteer_hours as vh')
            ->join('opportunities as o', 'vh.opportunity_id', '=', 'o.id')
            ->where('o.organization_id', $orgId)
            ->whereBetween('vh.date', [$from->toDateString(), $to->toDateString()])
            ->sum('vh.minutes');

        // Attendance stats (based on attendances.checkin_at in range)
        $attendanceBase = DB::table('attendances as a')
            ->join('opportunities as o', 'a.opportunity_id', '=', 'o.id')
            ->where('o.organization_id', $orgId)
            ->whereBetween(DB::raw('DATE(COALESCE(a.checkin_at, a.created_at))'), [$from->toDateString(), $to->toDateString()]);

        $attendanceTotal = (clone $attendanceBase)->count();
        $present = (clone $attendanceBase)->where('a.status','present')->count();
        $noShow  = (clone $attendanceBase)->where('a.status','no_show')->count();

        // Unfinalized attendance rows (needs finalize)
        $unfinalized = DB::table('attendances as a')
            ->join('opportunities as o', 'a.opportunity_id', '=', 'o.id')
            ->where('o.organization_id', $orgId)
            ->where(function($q){
                $q->whereNull('a.finalized_at')->orWhere('a.is_locked', false);
            })
            ->count();

        return [
            'total_minutes'    => $totalMinutes,
            'attendance_total' => $attendanceTotal,
            'present'          => $present,
            'no_show'          => $noShow,
            'unfinalized'      => $unfinalized,
        ];
    }
}
