<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\PublicSite\OpportunityPublicController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Opportunities
Route::prefix('opportunities')->group(function () {
    Route::get('/', [OpportunityPublicController::class, 'index'])->name('opportunities.index');
    // SEO-friendly: /opportunities/123-some-title (slug is optional)
    Route::get('/{opportunity}-{any?}', [OpportunityPublicController::class, 'show'])
        ->where(['opportunity' => '[0-9]+', 'any' => '.*'])
        ->name('opportunities.show');
});
