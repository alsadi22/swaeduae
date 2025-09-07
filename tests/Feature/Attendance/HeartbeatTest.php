<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeartbeatTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_auth(): void
    {
        $this->postJson('/api/v1/attendance/heartbeat')->assertUnauthorized();
    }

    public function test_stores_location_ping_and_returns_no_content(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/attendance/heartbeat', [
                'lat' => 24.1,
                'lng' => 54.3,
            ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('location_pings', [
            'user_id' => $user->id,
            'lat' => 24.1,
            'lng' => 54.3,
        ]);
    }

    public function test_throttles_requests(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 31; $i++) {
            $response = $this->actingAs($user, 'sanctum')
                ->postJson('/api/v1/attendance/heartbeat');
        }

        $response->assertStatus(429);
    }
}

