<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SmokeTest extends TestCase
{
    #[Test]
    public function public_endpoints_load_or_redirect_to_ok(): void
    {
        $this->withoutMiddleware([\Illuminate\Routing\Middleware\ThrottleRequests::class]);
        config(['cache.default' => 'array']);
        $uris = ['/', '/about', '/privacy', '/terms', '/contact', '/org/login'];
        foreach ($uris as $uri) {
            $res = $this->get($uri);
            $res = $res->isRedirection() ? $this->followRedirects($res) : $res;
            $res->assertOk();
            $this->assertNotEmpty($res->getContent());
        }
        $admin = $this->get('http://admin.swaeduae.ae/admin/login');
        $admin = $admin->isRedirection() ? $this->followRedirects($admin) : $admin;
        $admin->assertOk();
        $this->assertNotEmpty($admin->getContent());
    }
}
