<?php
if (file_exists(__DIR__.'/z_overrides.php')) require __DIR__.'/z_overrides.php';
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

    Route::get('/my/profile', [ProfileController::class, 'index'])->name('my.profile');
});

// QR
Route::match(['GET', 'POST'], '/qr/checkin', [\App\Http\Controllers\QR\CheckinController::class, 'checkin'])->name('qr.checkin.getpost');
Route::match(['GET', 'POST'], '/qr/checkout', [\App\Http\Controllers\QR\CheckinController::class, 'checkout'])->name('qr.checkout.getpost');
Route::get('/qr/verify/{serial?}', [VerifyController::class, 'show'])->name('qr.verify');

// Admin login alias → /login
Route::domain('admin.swaeduae.ae')->get('/admin/login', [AppHttpControllersAuthSimpleLoginController::class, 'show'])->name('admin');

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


// Public homepage (added automatically)
Route::get("/", function(){ return view("public.home"); })->name("home");
// Public homepage
Route::get("/", fn() => view("public.home"))->name("home");
// Public opportunities (UI stub)
Route::get("/opportunities", fn() => view("public.opportunities"))->name("opportunities.index");
// About (static)
Route::get("/about", fn() => view("public.about"))->name("about");
// Contact (GET form page; POST handled by contact.submit)
Route::get("/contact", fn() => view("public.contact"))->name("contact");

// Legacy path → QR verify (301, keep query & {code})
/* Admin domain routes (clean) */
Route::domain('admin.swaeduae.ae')
    ->middleware(['web','auth'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/', function () { return view('admin.dashboard'); })->name('dashboard');
        Route::get('/approvals', [\App\Http\Controllers\Admin\ApprovalsController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/orgs/{id}/approve', [\App\Http\Controllers\Admin\ApprovalsController::class, 'approveOrg'])->whereNumber('id')->name('approvals.orgs.approve');
        Route::post('/approvals/orgs/{id}/reject',  [\App\Http\Controllers\Admin\ApprovalsController::class, 'rejectOrg'])->whereNumber('id')->name('approvals.orgs.reject');
        Route::get('/hours', function(){ return view('admin.hours.index'); })->name('hours.index');
        Route::get('/certificates', function(){ return view('admin.certificates.index'); })->name('certificates.index');
    });

// Legacy path → QR verify (301, keep query & {code})
Route::get('/certificates/verify/{code?}', function ($code = null) {
    $target = '/qr/verify' . ($qs ? ('?' . http_build_query($qs)) : '');
    return redirect()->to($target, 301);
})->name('certificates.verify.form');
// Legacy path → QR verify (301, keep query & {code})
Route::get('/certificates/verify/{code?}', function ($code = null) {
    $qs = request()->query();
    if (!$qs && $code) { $qs = ['code' => $code]; }
    $target = '/qr/verify' . ($qs ? ('?' . http_build_query($qs)) : '');
    return redirect()->to($target, 301);
})->name('certificates.verify.form');

/* Admin domain routes (clean) */
Route::domain('admin.swaeduae.ae')
    ->middleware(['web','auth'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/', function () { return view('admin.dashboard'); })->name('dashboard');
        Route::get('/approvals', [\App\Http\Controllers\Admin\ApprovalsController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/orgs/{id}/approve', [\App\Http\Controllers\Admin\ApprovalsController::class, 'approveOrg'])->whereNumber('id')->name('approvals.orgs.approve');
        Route::post('/approvals/orgs/{id}/reject',  [\App\Http\Controllers\Admin\ApprovalsController::class, 'rejectOrg'])->whereNumber('id')->name('approvals.orgs.reject');
        Route::get('/hours', function(){ return view('admin.hours.index'); })->name('hours.index');
        Route::get('/certificates', function(){ return view('admin.certificates.index'); })->name('certificates.index');
    });
