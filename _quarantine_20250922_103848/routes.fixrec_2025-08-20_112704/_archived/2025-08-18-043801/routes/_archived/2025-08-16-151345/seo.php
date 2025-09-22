<?php

use Illuminate\Support\Facades\Route;

if (class_exists(\App\Http\Controllers\SitemapController::class)) {
    Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])
        ->name('sitemap.xml');
}
