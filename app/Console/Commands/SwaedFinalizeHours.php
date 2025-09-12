<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HoursService;

class SwaedFinalizeHours extends Command
{
    protected $signature = 'swaed:finalize-hours {opportunity_id?}';
    protected $description = 'Finalize volunteer hours from attendances (optionally for one opportunity)';

    public function handle()
    {
        $oid = $this->argument('opportunity_id');
        $n = HoursService::finalize($oid ? (int)$oid : null);
        $this->info("Finalized $n attendance records into volunteer_hours.");
        return self::SUCCESS;
    }
}
