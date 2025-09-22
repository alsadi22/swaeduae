<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/events', function () {
    if (class_exists(\App\Http\Controllers\EventController::class)) {
        return app(\App\Http\Controllers\EventController::class)->index(request());
    }
    // Fallback list from "events" table if controller/view missing
    $events = DB::table('events')->orderBy('start_at','desc')->limit(12)->get();
    if (view()->exists('events.index')) return view('events.index', compact('events'));
    return response()->view('layouts.app', [
        'slot' => view('partials._events_fallback', compact('events'))->render()
    ]);
})->name('events.index');

Route::get('/events/{id}', function ($id) {
    if (class_exists(\App\Http\Controllers\EventController::class)) {
        return app(\App\Http\Controllers\EventController::class)->show($id);
    }
    $event = DB::table('events')->where('id',$id)->first();
    abort_if(!$event, 404);
    if (view()->exists('events.show')) return view('events.show', compact('event'));
    return response()->view('layouts.app', [
        'slot' => "<div class='container py-4'><h1>{$event->title}</h1><p>{$event->description}</p></div>"
    ]);
})->whereNumber('id')->name('events.show');
