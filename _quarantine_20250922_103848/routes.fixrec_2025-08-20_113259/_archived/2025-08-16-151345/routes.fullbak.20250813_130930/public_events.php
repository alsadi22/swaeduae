<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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
    $event = DB::table('events')->where('id',$id)->first();
    abort_if(!$event, 404);
    return view()->exists('events.show')
        ? view('events.show', compact('event'))
        : response()->view('layouts.app', ['slot' => "<div class='container py-4'><h1>{$event->title}</h1><p>{$event->description}</p></div>"]);
})->whereNumber('id')->name('public.events.show');
