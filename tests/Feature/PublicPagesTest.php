<?php
namespace Tests\Feature;
use Tests\TestCase;
class PublicPagesTest extends TestCase {
    public function test_public_pages_render_with_public_layout() {
        $uris = ['/', '/about', '/privacy', '/terms', '/contact'];
        foreach ($uris as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee('class="public"', false);
        }
    }
}
