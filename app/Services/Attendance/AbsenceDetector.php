<?php

namespace App\Services\Attendance;

use App\Mail\VolunteerLeftAreaMail;
use App\Mail\VolunteerLeftAreaOrgMail;
use App\Models\Absence;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\LocationPing;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class AbsenceDetector
{
    public function scan(): void
    {
        $radius = (int) config('geofence.radius_meters', 150);
        $absenceMinutes = (int) config('geofence.absence_minutes', 30);
        $throttleMinutes = (int) config('geofence.email_throttle_minutes', 120);
        $now = now();

        $attendances = Attendance::whereNull('check_out_at')->get();
        foreach ($attendances as $att) {
            if ($att->lat === null || $att->lng === null || !$att->event_id) {
                continue;
            }
            $ping = LocationPing::where('user_id', $att->user_id)
                ->orderByRaw('COALESCE(captured_at, created_at) DESC')->first();
            if (!$ping) {
                continue;
            }
            $distance = $this->distanceMeters($att->lat, $att->lng, $ping->lat, $ping->lng);
            if ($distance > $radius && $ping->captured_at->lte($now->copy()->subMinutes($absenceMinutes))) {
                $absence = Absence::firstOrCreate(
                    [
                        'user_id' => $att->user_id,
                        'event_id' => $att->event_id,
                        'ended_at' => null,
                    ],
                    [
                        'started_at' => $ping->captured_at,
                    ]
                );
                if (!$absence->notified_at || $absence->notified_at->lte($now->copy()->subMinutes($throttleMinutes))) {
                    $event = Event::find($att->event_id);
                    Mail::to($att->user->email)->send(new VolunteerLeftAreaMail($event, $ping->captured_at));
                    if (method_exists($event, 'organization') && $event->organization && $event->organization->email) {
                        Mail::to($event->organization->email)->send(new VolunteerLeftAreaOrgMail($event, $ping->captured_at));
                    }
                    $absence->notified_at = $now;
                    $absence->save();
                }
            } else {
                $open = Absence::where('user_id',$att->user_id)
                    ->where('event_id',$att->event_id)
                    ->whereNull('ended_at')->first();
                if ($open && $distance <= $radius) {
                    $open->ended_at = $ping->captured_at;
                    $open->save();
                }
            }
        }
    }

    public function distanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $a = sin($dLat/2) ** 2 + cos($lat1) * cos($lat2) * sin($dLng/2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earth * $c;
    }
}
