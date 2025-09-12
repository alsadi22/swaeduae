<?php

namespace Tests\Feature\Org;

use App\Models\User;
use Tests\TestCase;

class AccessTest extends TestCase
{
    public function test_guest_redirected_from_org_dashboard(): void
    {
        $this->get('/org/dashboard')->assertRedirect('/login');
    }

    public function test_non_org_forbidden_from_org_dashboard(): void
    {
        $user = User::factory()->make(['role' => 'user']);
        $this->actingAs($user)->get('/org/dashboard')->assertStatus(403);
    }
}
