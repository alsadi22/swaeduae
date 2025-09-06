<?php

namespace Tests\Feature\Smoke;

use Tests\TestCase;

class PagesRenderTest extends TestCase
{
    public function test_public_pages_render(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/about')->assertStatus(200);
        $this->get('/contact')->assertStatus(200);
        $this->get('/qr/verify')->assertStatus(200);
    }

    public function test_admin_login_page_renders(): void
    {
        $this->get('/admin/login')->assertRedirect('/login');
        $this->get('/login')->assertStatus(200);
    }

    public function test_org_login_page_renders(): void
    {
        $this->get('/org/login')->assertStatus(200);
    }
}
