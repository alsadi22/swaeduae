<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeartbeatTest extends TestCase
{
    use RefreshDatabase;

    public function test_stores_location_ping(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/attendance/heartbeat?lat=24.1&lng=54.3');

        $response->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseCount('location_pings', 1);
        $ping = \DB::table('location_pings')->first();
        $this->assertEquals(24.1, (float) $ping->lat);
        $this->assertEquals(54.3, (float) $ping->lng);
        $this->assertEquals($user->id, $ping->user_id);
    }
}
