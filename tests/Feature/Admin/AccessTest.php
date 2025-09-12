<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Tests\TestCase;

class AccessTest extends TestCase
{
    public function test_guest_redirected_from_admin_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_non_admin_forbidden_from_admin_dashboard(): void
    {
        $user = User::factory()->make(['role' => 'user']);
        $this->actingAs($user)->get('/admin')->assertStatus(403);
    }
}
