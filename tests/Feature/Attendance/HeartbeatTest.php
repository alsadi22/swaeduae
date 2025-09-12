<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeartbeatTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_authentication(): void
    {
        $this->postJson('/api/v1/attendance/heartbeat', [
            'lat' => 24.1,
            'lng' => 54.3,
        ])->assertUnauthorized();
    }

    public function test_stores_valid_ping(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/attendance/heartbeat', [
                'lat' => 24.1,
                'lng' => 54.3,
                'accuracy' => 5,
            ])->assertNoContent();

        $this->assertDatabaseCount('location_pings', 1);
    }

    public function test_rejects_invalid_payload(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/attendance/heartbeat', [
                'lat' => 123,
            ])->assertStatus(422);
    }
}
