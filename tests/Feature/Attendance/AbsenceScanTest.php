<?php

namespace Tests\Feature\Attendance;

use App\Mail\VolunteerLeftAreaMail;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\LocationPing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AbsenceScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_absence_and_sends_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $event = Event::create(['title' => 'Test Event']);
        Attendance::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'check_in_at' => now()->subHour(),
            'lat' => 0,
            'lng' => 0,
        ]);
        LocationPing::create([
            'user_id' => $user->id,
            'lat' => 0,
            'lng' => 0.003,
            'captured_at' => now()->subMinutes(31),
        ]);

        $this->artisan('scan:volunteer-absences')->assertExitCode(0);

        $this->assertDatabaseCount('absences', 1);
        Mail::assertSent(VolunteerLeftAreaMail::class, 1);
    }
}
