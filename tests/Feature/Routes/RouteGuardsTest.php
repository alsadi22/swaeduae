<?php

namespace Tests\Feature\Routes;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class RouteGuardsTest extends TestCase
{
    public function test_admin_routes_require_admin_access(): void
    {
        $routes = collect(Route::getRoutes())
            ->filter(fn ($r) => Str::startsWith($r->uri(), 'admin/') && $r->uri() !== 'admin/login');

        $routes->each(function ($route) {
            $middleware = $route->gatherMiddleware();
            $this->assertContains('web', $middleware);
            $this->assertContains('auth', $middleware);
            $this->assertTrue(collect($middleware)->contains('can:admin-access'));
        });
    }

    public function test_org_routes_require_org_access_gate(): void
    {
        $routes = collect(Route::getRoutes())
            ->filter(fn ($r) => Str::startsWith($r->uri(), 'org/') && ! Str::contains($r->uri(), ['login', 'register']));

        $routes->each(function ($route) {
            $middleware = $route->gatherMiddleware();
            $this->assertContains('web', $middleware);
            $this->assertContains('auth', $middleware);
            $this->assertTrue(collect($middleware)->contains('can:org-access'));
        });
    }

    public function test_org_access_gate_hasrole_and_legacy_support(): void
    {
        $hasRoleUser = new class extends \Illuminate\Foundation\Auth\User
        {
            public function hasRole(string $role): bool
            {
                return $role === 'org';
            }
        };

        $legacyUser = new class extends \Illuminate\Foundation\Auth\User
        {
            public string $role = 'org';
        };

        $this->assertTrue(Gate::forUser($hasRoleUser)->allows('org-access'));
        $this->assertTrue(Gate::forUser($legacyUser)->allows('org-access'));
    }

    public function test_qr_verify_route_exists(): void
    {
        $this->get('/qr/verify')->assertStatus(200);

        // create minimal tables so controller does not fail when serial provided
        \Illuminate\Support\Facades\Schema::create('certificates', function ($table) {
            $table->id();
            $table->string('code');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->float('hours')->nullable();
        });
        \Illuminate\Support\Facades\Schema::create('users', function ($table) {
            $table->id();
            $table->string('name')->nullable();
        });
        \Illuminate\Support\Facades\Schema::create('events', function ($table) {
            $table->id();
            $table->string('title')->nullable();
        });

        $this->get('/qr/verify/ABC123')->assertStatus(200);
    }

    public function test_logout_route_is_unique(): void
    {
        $logoutRoutes = collect(Route::getRoutes())
            ->filter(fn ($r) => $r->uri() === 'logout');
        $this->assertCount(1, $logoutRoutes);
        $this->assertEquals('logout', $logoutRoutes->first()->getName());
    }
}
