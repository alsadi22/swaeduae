<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\OpportunityPublicController;

// Public opportunities (matches existing navbar name)
Route::get('/opportunities',        [OpportunityPublicController::class, 'index'])->name('opps.public.index');
Route::get('/opportunities/{id}',   [OpportunityPublicController::class, 'show'])->name('opps.public.show');
