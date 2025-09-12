<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VolunteerHoursService;
use App\Models\VolunteerHour;
use App\Models\Attendance;

class HoursRecalc extends Command
{
    protected $signature = 'hours:recalc {user_id} {opportunity_id}';
    protected $description = 'Recalculate and show minutes for a specific (user, opportunity) pair';

    public function handle(): int
    {
        $uid = (int) $this->argument('user_id');
        $oid = (int) $this->argument('opportunity_id');

        // Build a lightweight Attendance shell just to trigger the service path
        $a = new Attendance();
        $a->user_id = $uid; $a->opportunity_id = $oid;

        $svc = new VolunteerHoursService();
        $svc->storeOrUpdateFromAttendance($a);

        // If your service has totalForPair(), prefer that; otherwise re-derive via VolunteerHour.
        $stored = optional(
            VolunteerHour::where('user_id',$uid)->where('opportunity_id',$oid)->first()
        )->minutes;

        $this->line("pair=($uid,$oid) stored_minutes=" . ($stored ?? 'null'));
        return self::SUCCESS;
    }
}
