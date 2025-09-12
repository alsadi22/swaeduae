<?php
namespace Tests\Feature\Admin;

use Tests\TestCase;

class HostRedirectTest extends TestCase
{
    public function test_main_domain_admin_path_redirects_to_admin_host(): void
    {
        $response = $this->get('http://swaeduae.ae/admin/dashboard');
        $response->assertRedirect('https://admin.swaeduae.ae/admin/dashboard');
    }
}
