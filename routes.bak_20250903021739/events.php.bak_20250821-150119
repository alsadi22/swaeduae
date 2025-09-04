<?php
use App\Http\Controllers\Public\EventsBrowseController;


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

Route::get('/events', [EventController::class, 'index'])->name('events.index');

Route::get('/events/{idOrSlug}', [EventController::class, 'show'])
    ->where('idOrSlug', '^(?!browse$)(?:[0-9]+|[A-Za-z0-9\-]+)')
    ->name('events.show');
