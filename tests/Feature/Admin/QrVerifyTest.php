<?php

namespace Tests\Feature\Admin;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class QrVerifyTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_valid_code_shows_certificate(): void
    {
        $admin = $this->adminUser();
        Certificate::create([
            'uuid' => Str::uuid(),
            'code' => 'VALID123',
            'signature' => 'sig',
            'hours' => 2,
            'issued_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/qr/verify?code=VALID123')
            ->assertStatus(200)
            ->assertSee('Valid certificate');
    }

    public function test_invalid_code_shows_error(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->get('/admin/qr/verify?code=NOPE')
            ->assertStatus(200)
            ->assertSee('Code not found');
    }

    public function test_expired_code_shows_warning(): void
    {
        $admin = $this->adminUser();
        Certificate::create([
            'uuid' => Str::uuid(),
            'code' => 'OLD123',
            'signature' => 'sig',
            'hours' => 1,
            'issued_at' => now()->subDay(),
            'revoked_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/qr/verify?code=OLD123')
            ->assertStatus(200)
            ->assertSee('Certificate revoked');
    }
}
