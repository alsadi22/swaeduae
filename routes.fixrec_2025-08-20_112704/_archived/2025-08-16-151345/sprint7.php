<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Public\CalendarController;

// Calendar UI
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');

// ICS feeds
Route::get('/calendar.ics', [CalendarController::class, 'icsAll'])->name('calendar.ics');
Route::get('/opportunities/{id}.ics', [CalendarController::class, 'icsOpp'])->name('opps.public.ics');
Route::get('/events/{id}.ics', [CalendarController::class, 'icsEvent'])->name('events.public.ics');

// Minimal robots.txt
Route::get('/robots.txt', function() {
    return response("User-agent: *\nAllow: /\nSitemap: ".url('/sitemap.xml')."\n", 200, ['Content-Type'=>'text/plain']);
});

// Simple sitemap.xml (opportunities + events + static)
Route::get('/sitemap.xml', function() {
    $urls = [];
    $add = function($loc, $lastmod = null) use (&$urls) {
        $urls[] = ['loc'=>$loc, 'lastmod'=>$lastmod];
    };
    $add(url('/'));
    $add(route('about'));
    $add(route('faq'));
    $add(route('partners'));
    if (Schema::hasTable('opportunities')) {
        $rows = DB::table('opportunities')->select('id','updated_at')->orderBy('id','desc')->limit(500)->get();
        foreach ($rows as $r) $add(url('/opportunities/'.$r->id), $r->updated_at ? date('c', strtotime($r->updated_at)) : null);
    }
    if (Schema::hasTable('events')) {
        $rows = DB::table('events')->select('id','updated_at')->orderBy('id','desc')->limit(500)->get();
        foreach ($rows as $r) $add(url('/events/'.$r->id), $r->updated_at ? date('c', strtotime($r->updated_at)) : null);
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $xml.= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    foreach ($urls as $u) {
        $xml.= '  <url><loc>'.htmlspecialchars($u['loc'], ENT_XML1).'</loc>';
        if (!empty($u['lastmod'])) $xml.= '<lastmod>'.$u['lastmod'].'</lastmod>';
        $xml.= '</url>'."\n";
    }
    $xml.= '</urlset>';
    return response($xml, 200, ['Content-Type'=>'application/xml']);
})->name('sitemap.xml');
