<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Services\AttendanceService;

class BackfillVolunteerHours extends Command
{
    protected $signature = 'swaed:backfill-hours {--from-id= : Start attendance ID} {--limit=1000 : Max rows to process}';
    protected $description = 'Backfill volunteer hours from existing attendance records';

    public function handle(AttendanceService $attendanceService): int
    {
        $fromId = (int)($this->option('from-id') ?: 0);
        $limit  = (int)$this->option('limit');

        $query = Attendance::query()
            ->when($fromId > 0, fn($q)=>$q->where('id','>=',$fromId))
            ->whereNotNull('check_in_at')
            ->whereNotNull('check_out_at')
            ->orderBy('id')
            ->limit($limit);

        $count = 0;
        $query->chunkById(200, function ($chunk) use ($attendanceService, &$count) {
            foreach ($chunk as $attendance) {
                $attendanceService->computeHoursFromAttendance($attendance);
                $count++;
            }
        });

        $this->info("Processed {$count} attendance rows.");
        return self::SUCCESS;
    }
}
