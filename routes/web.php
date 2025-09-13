<?php
if (file_exists(__DIR__.'/z_overrides.php')) require __DIR__.'/z_overrides.php';
Route::view('/', 'public.home')->name('home.public');

use App\Http\Controllers\Auth\AuthenticatedSessionController;
// use App\Http\Controllers\Auth\SimpleLoginController;
use App\Http\Controllers\Auth\SimplePasswordResetController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CertificatePdfController;
use App\Http\Controllers\ContactController;
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

    Route::get('/certificates', [CertificateController::class, 'index'])->name('legacy_admin.certificates.index');
    Route::get('/certificates/{id}/download', [CertificatePdfController::class, 'download'])->whereNumber('id')->name('certificates.download');
    Route::post('/certificates/{id}/resend', [CertificatePdfController::class, 'resend'])->whereNumber('id')->name('certificates.resend');
    Route::post('/certificates/{id}/revoke', [CertificatePdfController::class, 'revoke'])->whereNumber('id')->name('certificates.revoke');

});

// QR
Route::match(['GET', 'POST'], '/qr/checkin', [\App\Http\Controllers\QR\CheckinController::class, 'checkin'])->name('qr.checkin.getpost');
Route::match(['GET', 'POST'], '/qr/checkout', [\App\Http\Controllers\QR\CheckinController::class, 'checkout'])->name('qr.checkout.getpost');
Route::get('/qr/verify/{serial?}', [VerifyController::class, 'show'])->name('qr.verify');

// Admin login alias → /login
// Route::domain('admin.swaeduae.ae')->get('/admin/login', [AppHttpControllersAuthSimpleLoginController::class, 'show'])->name('admin');

// Guest auth
Route::middleware(['web', 'guest', 'throttle:10,1'])->group(function () {
//     Route::get('/login', [SimpleLoginController::class, 'show'])->name('login');
//     Route::post('/login', [SimpleLoginController::class, 'perform'])->name('login.perform');
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
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () { return view('admin.dashboard'); })->name('dashboard');
        Route::get('/approvals', [\App\Http\Controllers\Admin\ApprovalsController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/orgs/{id}/approve', [\App\Http\Controllers\Admin\ApprovalsController::class, 'approveOrg'])->whereNumber('id')->name('approvals.orgs.approve');
        Route::post('/approvals/orgs/{id}/reject',  [\App\Http\Controllers\Admin\ApprovalsController::class, 'rejectOrg'])->whereNumber('id')->name('approvals.orgs.reject');
        Route::get('/hours', function(){ return view('admin.hours.index'); })->name('hours.index');
        Route::get('/certificates', function(){ return view('admin.certificates.index'); })->name('legacy_admin.certificates.index');
    });

// Legacy path → QR verify (301, keep query & {code})
// Legacy path → QR verify (301, keep query & {code})
/* Admin domain routes (clean) */
Route::domain('admin.swaeduae.ae')
    ->middleware(['web','auth'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () { return view('admin.dashboard'); })->name('dashboard');
        Route::get('/approvals', [\App\Http\Controllers\Admin\ApprovalsController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/orgs/{id}/approve', [\App\Http\Controllers\Admin\ApprovalsController::class, 'approveOrg'])->whereNumber('id')->name('approvals.orgs.approve');
        Route::post('/approvals/orgs/{id}/reject',  [\App\Http\Controllers\Admin\ApprovalsController::class, 'rejectOrg'])->whereNumber('id')->name('approvals.orgs.reject');
        Route::get('/hours', function(){ return view('admin.hours.index'); })->name('hours.index');
        Route::get('/certificates', function(){ return view('admin.certificates.index'); })->name('legacy_admin.certificates.index');
    });

// == Alias route for test compatibility (decline -> reject) ==
Route::middleware(['web','auth','can:admin-access'])
    ->domain('admin.swaeduae.ae')
    ->prefix('approvals')
    ->as('admin.approvals.')
    ->group(function () {
        Route::post('orgs/{id}/decline', [\App\Http\Controllers\Admin\ApprovalsController::class, 'rejectOrg'])
            ->whereNumber('id')->name('orgs.decline');
    });
// Home route
Route::get('/', function () { return view('public.home'); })->name('home');
// Admin Approvals (domain-guarded)
Route::domain('admin.swaeduae.ae')
    ->middleware(['web','auth','can:admin-access'])
    ->name('admin.')
    ->group(function () {
        Route::get('/approvals', [\App\Http\Controllers\Admin\ApprovalsController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/orgs/{id}/approve', [\App\Http\Controllers\Admin\ApprovalsController::class, 'approveOrg'])->whereNumber('id')->name('approvals.orgs.approve');
        Route::post('/approvals/orgs/{id}/reject',  [\App\Http\Controllers\Admin\ApprovalsController::class, 'rejectOrg'])->whereNumber('id')->name('approvals.orgs.reject');
    });
// Admin root -> Approvals
Route::domain('admin.swaeduae.ae')->get('/', function () {
    return redirect('/approvals');
})->name('admin.root');
Route::get('/certificates/verify/{code?}', function ($code = null) {
    return $code
        ? redirect('/qr/verify/'.$code, 302)
        : redirect('/qr/verify', 302);
})->name('certificates.verify.form');
Route::get('/events/browse', function () {
    return redirect('/events', 302);
})->name('events.browse');

// == Volunteer Self Service Pages ==
Route::middleware(['web','auth'])->group(function () {
});

// == Volunteer Self Service Pages ==

Route::middleware(['web','auth'])->group(function () {
});

// == Volunteer Self Service Pages ==
Route::middleware(['web','auth'])->group(function () {
    Route::get('/my/profile', fn() => view('volunteer.profile'))->name('volunteer.profile');
    Route::get('/my/settings', fn() => view('volunteer.settings'))->name('volunteer.settings');
});
Route::view('/volunteers', 'public.volunteers')->name('public.volunteers');
Route::view('/organizations', 'public.organizations')->name('public.organizations');
Route::view('/stories', 'public.stories')->name('public.stories');

// == Volunteer extra pages ==
Route::middleware(['web','auth'])->group(function () {
    Route::view('/my/applications', 'volunteer.applications')->name('volunteer.applications');
    Route::view('/my/certificates', 'volunteer.certificates.index')->name('volunteer.certificates');
    Route::view('/my/hours', 'volunteer.hours')->name('volunteer.hours');
    Route::view('/my/notifications', 'volunteer.notifications')->name('volunteer.notifications');
});
