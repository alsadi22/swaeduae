<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Services\VolunteerHoursService;
use Illuminate\Support\Str;

class AttendanceObserver
{
    public function creating(Attendance $attendance): void
    {
        if (empty($attendance->token)) {
            $attendance->token = (string) Str::uuid();
        }
    }

    public function saved(Attendance $attendance): void
    {
        (new VolunteerHoursService())->storeOrUpdateFromAttendance($attendance);
    }

    public function deleted(Attendance $attendance): void
    {
        (new VolunteerHoursService())->storeOrUpdateFromAttendance($attendance);
    }
}
