<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SeoFallbackTest extends TestCase
{
    public function test_route_based_seo_fallback_applies_when_no_sections_set(): void
    {
        // Define a temporary route named 'about' that renders only the SEO meta component
        Route::get('/__seo_test', fn () => view('_test.seo-min'))->name('about');

        $res = $this->get('/__seo_test');
        $res->assertOk();

        $title = config('seo.pages.about.title');
        $desc  = config('seo.pages.about.description');

        $res->assertSee("<title>{$title}</title>", false);
        $res->assertSee('<meta name="description" content="'.$desc.'">', false);
        $res->assertSee('<meta property="og:title" content="'.$title.'">', false);
        $res->assertSee('<meta property="og:description" content="'.$desc.'">', false);
    }
}

