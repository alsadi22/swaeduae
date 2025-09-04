<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SmokeTest extends TestCase
{
    #[Test]
    public function public_endpoints_load_or_redirect_to_ok(): void
    {
        foreach (['/', '/about', '/faq', '/contact', '/opportunities'] as $uri) {
            $res = $this->get($uri);
            $res = $res->isRedirection() ? $this->followRedirects($res) : $res;
            $res->assertOk();
        }
    }
}
