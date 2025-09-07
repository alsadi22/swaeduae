<?php
namespace Tests\Feature;
use Tests\TestCase;
class SeoMetaTest extends TestCase
{
    public function test_about_page_has_canonical_and_og_tags(): void
    {
        $res = $this->get('/about');
        $res->assertOk();
        $res->assertSee('rel="canonical"', false);
        $res->assertSee('property="og:title"', false);
        $res->assertSee('property="og:description"', false);
    }
    public function test_home_page_has_canonical_and_og_tags(): void
    {
        $res = $this->get('/');
        $res->assertOk();
        $res->assertSee('rel="canonical"', false);
        $res->assertSee('property="og:title"', false);
        $res->assertSee('property="og:description"', false);
    }
}
