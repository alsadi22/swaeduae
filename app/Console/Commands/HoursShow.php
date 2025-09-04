<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VolunteerHoursService;
use App\Models\VolunteerHour;

class HoursShow extends Command
{
    protected $signature = 'hours:show {user_id} {opportunity_id}';
    protected $description = 'Show calculated minutes for a specific (user, opportunity) pair and current VolunteerHour value';

    public function handle(): int
    {
        $uid = (int) $this->argument('user_id');
        $oid = (int) $this->argument('opportunity_id');

        $svc = new VolunteerHoursService();
        $calc = $svc->totalForPair($uid, $oid);

        $vh = VolunteerHour::where('user_id', $uid)->where('opportunity_id', $oid)->first();
        $stored = $vh ? (int) $vh->minutes : null;

        $this->line("pair=($uid,$oid) calc_minutes=$calc stored_minutes=" . ($stored ?? 'null'));

        return self::SUCCESS;
    }
}
