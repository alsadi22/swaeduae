<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Support\Facades\Response;

class SeoController extends Controller
{
    public function sitemap()
    {
        $items = Opportunity::where('is_published', true)->orderByDesc('updated_at')->limit(1000)->get();
        return response()->view('seo.sitemap', compact('items'))->header('Content-Type','application/xml');
    }

    public function robots()
    {
        return Response::make("User-agent: *\nAllow: /\nSitemap: ".route('sitemap'), 200, ['Content-Type'=>'text/plain']);
    }
}
