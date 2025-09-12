<?php
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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
    $opportunity = DB::table('opportunities')->where('id',$id)->first();
    abort_if(!$opportunity, 404);
    return view()->exists('opportunities.show')
        ? view('opportunities.show', compact('opportunity'))
        : response()->view('layouts.app', ['slot' => "<div class='container py-4'><h1>{$opportunity->title}</h1><p>{$opportunity->description}</p></div>"]);
})->whereNumber('id')->name('public.opportunities.show');
