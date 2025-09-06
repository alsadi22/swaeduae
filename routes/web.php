<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SimpleLoginController;
use App\Http\Controllers\Auth\SimplePasswordResetController;

use App\Http\Controllers\QR\VerifyController;
use App\Http\Controllers\CertificatePdfController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\My\ProfileController;

require __DIR__.'/partials/disable_internal.php';

// ==== FORCE admin paths to admin subdomain (TOP GUARD) ====
Route::domain(env('MAIN_DOMAIN','swaeduae.ae'))->middleware(['web'])->group(function () {
    Route::any('/admin{any?}', function ($any = null) {
        $target = 'https://' . env('ADMIN_DOMAIN','admin.swaeduae.ae') . '/admin' . ($any ? '/' . ltrim($any,'/') : '');
        return redirect()->away($target, 301);
    })->where('any','.*')->name('main.admin.redirect');
});

// Public
Route::get('/partners', fn () => view('public.partners'))->name('partners.index');

// Auth + verified area
Route::middleware(['web','auth','verified'])->group(function () {
    Route::get('/applications', fn () => view('applications.index'))->name('applications.index');

    Route::get('/certificates', [CertificateController::class,'index'])->name('certificates.index');
    Route::get('/certificates/{id}/download', [CertificatePdfController::class,'download'])->whereNumber('id')->name('certificates.download');
    Route::post('/certificates/{id}/resend',   [CertificatePdfController::class,'resend'])->whereNumber('id')->name('certificates.resend');
    Route::post('/certificates/{id}/revoke',   [CertificatePdfController::class,'revoke'])->whereNumber('id')->name('certificates.revoke');

    Route::get('/my/profile', [ProfileController::class,'index'])->name('my.profile');
});

// QR
Route::match(['GET','POST'],'/qr/checkin',  [\App\Http\Controllers\QR\CheckinController::class,'checkin'])->name('qr.checkin.getpost');
Route::match(['GET','POST'],'/qr/checkout', [\App\Http\Controllers\QR\CheckinController::class,'checkout'])->name('qr.checkout.getpost');
Route::get('/qr/verify/{serial?}', [VerifyController::class,'show'])->name('qr.verify');

// Admin login alias → /login
Route::get('/admin/login', fn () => redirect()->to('/login'))->name('admin.login');

// Guest auth
Route::middleware(['web','guest','throttle:10,1'])->group(function () {
    Route::get('/login',  [SimpleLoginController::class, 'show'])->name('login');
    Route::post('/login', [SimpleLoginController::class, 'perform'])->name('login.perform');
});

// Logout (auth)
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['web','auth'])
    ->name('logout');

// Password reset (guest)
Route::middleware(['web','guest','throttle:10,1'])
    ->withoutMiddleware([\App\Http\Middleware\EnforceOrgRegistration::class, \App\Http\Middleware\MicroCache::class])
    ->group(function () {
        Route::get('/reset-password/{token}', [SimplePasswordResetController::class, 'show'])->name('password.reset');
        Route::post('/reset-password',        [SimplePasswordResetController::class, 'update'])->name('password.update.simple');
    });

// ==== Admin routes (verified + gate) ====
Route::middleware(['web','auth','verified','can:admin-access'])
    ->withoutMiddleware([\App\Http\Middleware\EnforceOrgRegistration::class])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class,'index'])->name('dashboard');

        // Users
        Route::get('/users',               [\App\Http\Controllers\Admin\UserController::class,'index'])->name('users.index');
        Route::get('/users/{user}',        [\App\Http\Controllers\Admin\UserController::class,'show'])->name('users.show');
        Route::put('/users/{user}',        [\App\Http\Controllers\Admin\UserController::class,'update'])->name('users.save');
        Route::patch('/users/{user}',      [\App\Http\Controllers\Admin\UserController::class,'update']);
        Route::delete('/users/{user}',     [\App\Http\Controllers\Admin\UserController::class,'destroy'])->name('users.destroy');

        // Organizations
        Route::get('/organizations',               [\App\Http\Controllers\Admin\OrganizationsController::class,'index'])->name('organizations.index');
        Route::post('/organizations/{id}/approve', [\App\Http\Controllers\Admin\OrganizationsController::class,'approve'])->name('organizations.approve');
        Route::post('/organizations/{id}/suspend', [\App\Http\Controllers\Admin\OrganizationsController::class,'suspend'])->name('organizations.suspend');

        // Opportunities
        Route::get('/opportunities',                  [\App\Http\Controllers\Admin\OpportunityController::class,'index'])->name('opportunities.index');
        Route::get('/opportunities/{opportunity}',    [\App\Http\Controllers\Admin\OpportunityController::class,'show'])->name('opportunities.show');
        Route::put('/opportunities/{opportunity}',    [\App\Http\Controllers\Admin\OpportunityController::class,'update'])->name('opportunities.save');
        Route::patch('/opportunities/{opportunity}',  [\App\Http\Controllers\Admin\OpportunityController::class,'update']);
        Route::delete('/opportunities/{opportunity}', [\App\Http\Controllers\Admin\OpportunityController::class,'destroy'])->name('opportunities.destroy');
    });
