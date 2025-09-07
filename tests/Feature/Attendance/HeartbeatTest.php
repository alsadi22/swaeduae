<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HeartbeatTest extends TestCase
{
    use RefreshDatabase;

    public function test_route_is_registered(): void
    {
        $route = Route::getRoutes()->getByName('api.attendance.heartbeat');
        $this->assertNotNull($route);
        $this->assertContains('POST', $route->methods());
    }

    public function test_guest_is_redirected_and_no_row_created(): void
    {
        $this->post('/api/v1/attendance/heartbeat', [
            'lat' => 24.1,
            'lng' => 54.3,
        ])->assertRedirect('/login');

        $this->assertDatabaseCount('location_pings', 0);
    }

    public function test_authenticated_user_creates_location_ping(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/api/v1/attendance/heartbeat', [
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
}
