<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['web','auth','org:org'])
    ->prefix('org')->as('org.')
    ->group(function () {
        Route::get('/opportunities/{opportunity}/shortlist', [\App\Http\Controllers\Org\ShortlistController::class, 'index'])->name('shortlist.index');
        Route::post('/opportunities/{opportunity}/shortlist/bulk', [\App\Http\Controllers\Org\ShortlistController::class, 'bulk'])->name('shortlist.bulk');
        Route::post('/opportunities/{opportunity}/shortlist/slot-cap', [\App\Http\Controllers\Org\ShortlistController::class, 'updateSlotCap'])->name('shortlist.slot_cap');
    });

