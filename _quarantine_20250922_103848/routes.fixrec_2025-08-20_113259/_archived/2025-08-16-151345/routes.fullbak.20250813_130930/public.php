<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/**
 * HOME
 */
Route::get('/', function () {
    return view()->exists('welcome') ? view('welcome') : redirect('/opportunities');
})->name('home');

/**
 * OPPORTUNITIES (names used in Blade: public.opportunities, public.opportunities.show)
 */
Route::get('/opportunities', function () {
    if (class_exists(\App\Http\Controllers\OpportunityController::class)) {
        return app(\App\Http\Controllers\OpportunityController::class)->index(request());
    }
    $opportunities = DB::table('opportunities')->orderByDesc('created_at')->paginate(12);
    return view()->exists('opportunities.index')
        ? view('opportunities.index', compact('opportunities'))
        : view('partials._opps_fallback', compact('opportunities'));
})->name('public.opportunities');

Route::get('/opportunities/{id}', function ($id) {
    if (class_exists(\App\Http\Controllers\OpportunityController::class)) {
        return app(\App\Http\Controllers\OpportunityController::class)->show($id);
    }
    $o = DB::table('opportunities')->where('id', $id)->first();
    abort_if(!$o, 404);
    return view()->exists('opportunities.show')
        ? view('opportunities.show', ['opportunity' => $o])
        : response()->view('layouts.app', ['slot' => "<div class='container py-4'><h1>{$o->title}</h1><p>{$o->description}</p></div>"]);
})->whereNumber('id')->name('public.opportunities.show');

/**
 * EVENTS (names used in Blade: public.events, public.events.show)
 */
Route::get('/events', function () {
    if (class_exists(\App\Http\Controllers\EventController::class)) {
        return app(\App\Http\Controllers\EventController::class)->index(request());
    }
    $events = DB::table('events')->orderByDesc('start_at')->limit(12)->get();
    return view()->exists('events.index')
        ? view('events.index', compact('events'))
        : view('partials._events_fallback', compact('events'));
})->name('public.events');

Route::get('/events/{id}', function ($id) {
    if (class_exists(\App\Http\Controllers\EventController::class)) {
        return app(\App\Http\Controllers\EventController::class)->show($id);
    }
    $e = DB::table('events')->where('id', $id)->first();
    abort_if(!$e, 404);
    return view()->exists('events.show')
        ? view('events.show', ['event' => $e])
        : response()->view('layouts.app', ['slot' => "<div class='container py-4'><h1>{$e->title}</h1><p>{$e->description}</p></div>"]);
})->whereNumber('id')->name('public.events.show');

/**
 * ORGANIZATIONS & GALLERY (names used in Blade: public.organizations, public.gallery)
 */
Route::get('/organizations', function () {
    return view()->exists('pages.organizations')
        ? view('pages.organizations')
        : view('welcome')->with('message', __('Organizations page coming soon.'));
})->name('public.organizations');

Route::get('/gallery', function () {
    return view()->exists('pages.gallery')
        ? view('pages.gallery')
        : view('welcome')->with('message', __('Gallery page coming soon.'));
})->name('public.gallery');
