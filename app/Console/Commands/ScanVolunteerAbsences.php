<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Attendance\AbsenceDetector;

class ScanVolunteerAbsences extends Command
{
    protected $signature = 'scan:volunteer-absences';
    protected $description = 'Scan volunteer location pings to detect absences';

    public function handle(AbsenceDetector $detector): int
    {
        $detector->scan();
        return self::SUCCESS;
    }
}
