<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VolunteerHoursService;

class ReconcileVolunteerHours extends Command
{
    protected $signature = 'hours:reconcile';
    protected $description = 'Recalculate volunteer minutes from all attendances';

    public function handle(VolunteerHoursService $svc): int
    {
        $svc->reconcileAll();
        $this->info('Volunteer hours reconciliation complete.');
        return self::SUCCESS;
    }
}
