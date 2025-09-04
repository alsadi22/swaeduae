<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Http\Controllers\Public\OrganizationPublicController;
use App\Http\Controllers\Public\PartnerIntakeController;

// Throttle partner submissions: 10/hour per IP
if (class_exists(RateLimiter::class)) {
    RateLimiter::for('partners', function (Request $request) {
        return Limit::perHour(10)->by($request->ip());
    });
}

// Public organizations
Route::get('/organizations',                 [OrganizationPublicController::class, 'index'])->name('orgs.public.index');
Route::get('/organizations/{id}-{slug?}',    [OrganizationPublicController::class, 'show'])->name('orgs.public.show');

// Partner intake
Route::get('/partners/apply',  [PartnerIntakeController::class, 'form'])->name('partners.apply.form');
Route::post('/partners/apply', [PartnerIntakeController::class, 'submit'])
    ->middleware('throttle:partners')
    ->name('partners.apply.submit');

// Optional: legacy alias if any old links used /partner/apply
Route::get('/partner/apply', fn() => redirect()->route('partners.apply.form'))->name('partner.apply.legacy');
