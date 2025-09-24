<?php
require __DIR__.'/z_pre_overrides.php';
Route::view('/', 'public.home')->name('home.public');

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SimpleLoginController;
use App\Http\Controllers\Auth\SimplePasswordResetController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CertificatePdfController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ApprovalsController;
use App\Http\Controllers\My\ProfileController;
use App\Http\Controllers\QR\VerifyController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/partials/disable_internal.php';

// ==== FORCE admin paths to admin subdomain (TOP GUARD) ====
Route::domain(env('MAIN_DOMAIN', 'swaeduae.ae'))->middleware(['web'])->group(function () {
    Route::any('/admin{any?}', function ($any = null) {
        $target = 'https://'.env('ADMIN_DOMAIN', 'admin.swaeduae.ae').'/admin'.($any ? '/'.ltrim($any, '/') : '');

        return redirect()->away($target, 301);
    })->where('any', '.*')->name('main.admin.redirect');
});

// Public
Route::view('/about', 'public.about')->name('about');
Route::view('/privacy', 'public.privacy')->name('privacy');
Route::view('/terms', 'public.terms')->name('terms');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.get');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,1')->name('contact.submit');
Route::get('/partners', fn () => view('public.partners'))->name('partners.index');

// Auth + verified area
Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/applications', fn () => view('applications.index'))->name('applications.index');

    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{id}/download', [CertificatePdfController::class, 'download'])->whereNumber('id')->name('certificates.download');
    Route::post('/certificates/{id}/resend', [CertificatePdfController::class, 'resend'])->whereNumber('id')->name('certificates.resend');
    Route::post('/certificates/{id}/revoke', [CertificatePdfController::class, 'revoke'])->whereNumber('id')->name('certificates.revoke');

    Route::middleware(['web','auth','verified'])->get('/my/profile', function(){ return view('my.profile'); });
});

// QR
Route::match(['GET', 'POST'], '/qr/checkin', [\App\Http\Controllers\QR\CheckinController::class, 'checkin'])->name('qr.checkin.getpost');
Route::match(['GET', 'POST'], '/qr/checkout', [\App\Http\Controllers\QR\CheckinController::class, 'checkout'])->name('qr.checkout.getpost');
Route::get('/qr/verify/{serial?}', [VerifyController::class, 'show'])->name('qr.verify');

// Admin login alias → /login
Route::get('/admin/login', fn () => redirect()->to('/login'))->name('admin.login');

Route::middleware(['web','auth','can:admin-access'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/approvals',[ApprovalsController::class,'index'])->name('approvals.index');
    Route::post('/approvals/orgs/{id}/approve',[ApprovalsController::class,'approveOrg'])->whereNumber('id')->name('approvals.orgs.approve');
    Route::post('/approvals/orgs/{id}/decline',[ApprovalsController::class,'declineOrg'])->whereNumber('id')->name('approvals.orgs.decline');
});

// Guest auth
Route::middleware(['web', 'guest', 'throttle:10,1'])->group(function () {
    Route::get('/login', [SimpleLoginController::class, 'show'])->name('login');
    Route::post('/login', [SimpleLoginController::class, 'perform'])->name('login.perform');
});

// Logout (auth)
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['web', 'auth'])
    ->name('logout');

// Password reset (guest)
Route::middleware(['web', 'guest', 'throttle:10,1'])
    ->withoutMiddleware([\App\Http\Middleware\EnforceOrgRegistration::class, \App\Http\Middleware\MicroCache::class])
    ->group(function () {
        Route::get('/reset-password/{token}', [SimplePasswordResetController::class, 'show'])->name('password.reset');
        Route::post('/reset-password', [SimplePasswordResetController::class, 'update'])->name('password.update.simple');
    });
/** Certificates verify canonical */
Route::get('/certificates/verify/{code?}', function (?string $code = null) {
    return view('public.certificates.verify', ['code' => $code]);
})->name('certificates.verify.form');

require __DIR__ . '/z_canonical.php';

// Load public fallback routes (safe if primaries missing)

Route::view('/faq','public.faq')->name('faq');
Route::view('/events','public.events.index')->name('events.index');
