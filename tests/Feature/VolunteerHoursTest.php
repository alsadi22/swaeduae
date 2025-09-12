<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VolunteerHoursTest extends TestCase
{
    use RefreshDatabase;

    public function test_reconcile_sums_minutes(): void
    {
        $user = \App\Models\User::factory()->create();
        $opp  = \App\Models\Opportunity::factory()->create();

        // Use a single, minute-aligned base time to avoid truncation drift
        $base = now()->startOfMinute();

        \App\Models\Attendance::create([
            'user_id'       => $user->id,
            'opportunity_id'=> $opp->id,
            'checkin_at'    => $base->copy()->subHours(3),
            'checkout_at'   => $base->copy()->subHours(1),
            'token'         => (string) \Illuminate\Support\Str::uuid(),
        ]);

        \Artisan::call('hours:reconcile');

        $vh = \App\Models\VolunteerHour::whereUserId($user->id)
            ->whereOpportunityId($opp->id)
            ->first();

        $this->assertNotNull($vh);
        $this->assertSame(120, (int) $vh->minutes);
    }
}
