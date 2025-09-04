<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/opportunities', function () {
    if (class_exists(\App\Http\Controllers\OpportunityController::class)) {
        return app(\App\Http\Controllers\OpportunityController::class)->index(request());
    }
    $opportunities = DB::table('opportunities')->orderBy('created_at','desc')->paginate(12);
    if (view()->exists('opportunities.index')) return view('opportunities.index', compact('opportunities'));
    return response()->view('layouts.app', [
        'slot' => view('partials._opps_fallback', compact('opportunities'))->render()
    ]);
})->name('opportunities.index');

Route::get('/opportunities/{id}', function ($id) {
    if (class_exists(\App\Http\Controllers\OpportunityController::class)) {
        return app(\App\Http\Controllers\OpportunityController::class)->show($id);
    }
    $opportunity = DB::table('opportunities')->where('id',$id)->first();
    abort_if(!$opportunity, 404);
    if (view()->exists('opportunities.show')) return view('opportunities.show', compact('opportunity'));
    return response()->view('layouts.app', [
        'slot' => "<div class='container py-4'><h1>{$opportunity->title}</h1><p>{$opportunity->description}</p></div>"
    ]);
})->whereNumber('id')->name('opportunities.show');
