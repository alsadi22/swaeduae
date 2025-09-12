<?php

namespace Tests\Feature\Smoke;

use Tests\TestCase;

class PagesRenderTest extends TestCase
{
    public function test_public_pages_render(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertGreaterThan(0, strlen($response->getContent()));
        $this->get('/about')->assertStatus(200);
        $this->get('/contact')->assertStatus(200);
        $this->get('/qr/verify')->assertStatus(200);
    }

    public function test_admin_login_page_renders(): void
    {
        config(['cache.default' => 'array']);
        $this->withoutMiddleware([\Illuminate\Routing\Middleware\ThrottleRequests::class]);
        $this->get('/admin/login')->assertRedirect();
        $this->get('/login')->assertStatus(200);
    }

    public function test_org_login_page_renders(): void
    {
        config(['cache.default' => 'array']);
        $this->withoutMiddleware([\Illuminate\Routing\Middleware\ThrottleRequests::class]);
        $this->get('/org/login')->assertStatus(200);
    }
}
