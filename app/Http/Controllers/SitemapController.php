<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $base = rtrim(config('app.url') ?? url('/'), '/');
        $urls = [
            ['loc' => $base.'/',          'changefreq' => 'daily',   'priority' => '1.0'],
            ['loc' => $base.'/opportunities', 'changefreq' => 'hourly', 'priority' => '0.9'],
            ['loc' => $base.'/register',  'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $base.'/org/register', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $base.'/contact',   'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $base.'/about',     'changefreq' => 'yearly',  'priority' => '0.4'],
            ['loc' => $base.'/privacy',   'changefreq' => 'yearly',  'priority' => '0.3'],
            ['loc' => $base.'/terms',     'changefreq' => 'yearly',  'priority' => '0.3'],
        ];
        $xml = view('sitemap', compact('urls'))->render();
        return response($xml, 200)->header('Content-Type', 'application/xml')->setSharedMaxAge(3600);
    }
}
