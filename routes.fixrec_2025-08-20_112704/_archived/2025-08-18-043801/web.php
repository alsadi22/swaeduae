<?php

require __DIR__.'/_admin_auth.php';
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/healthz', fn () => response('ok', 200))->name('health');

/** Public site */
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
Route::get('/events/{id}', [\App\Http\Controllers\EventController::class, 'show'])->name('events.show');
Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/news/{id}', [\App\Http\Controllers\NewsController::class, 'show'])->name('news.show');
Route::get('/downloads', [\App\Http\Controllers\DownloadController::class, 'index'])->name('downloads.index');
Route::get('/region/sharjah', [\App\Http\Controllers\EventController::class, 'sharjah'])->name('region.sharjah');
Route::get('/partners', [\App\Http\Controllers\PartnerController::class, 'index'])->name('partners.index');

Route::post('/lang/switch', function (Request $request) {
    $lang = $request->input('lang');
    if (in_array($lang, ['en', 'ar'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }
    return back();
})->name('lang.switch');

/** Authenticated user actions */
Route::middleware('auth')->group(function () {
    Route::get('/volunteer/profile', \App\Http\Controllers\Volunteer\ProfileIndexAction::class)->name('volunteer.profile');
    Route::post('/volunteer/events/{eventId}/register', [\App\Http\Controllers\VolunteerController::class, 'registerEvent'])->name('volunteer.registerEvent');
    Route::post('/volunteer/kyc/upload', [\App\Http\Controllers\VolunteerController::class, 'uploadKyc'])->name('volunteer.uploadKyc');
    Route::get('/volunteer/resume', [\App\Http\Controllers\VolunteerController::class, 'resume'])->name('volunteer.resume');
    Route::get('/volunteer/certificate/{certId}', [\App\Http\Controllers\VolunteerController::class, 'generateCertificate'])->name('volunteer.generateCertificate');

    Route::get('/payment', [\App\Http\Controllers\PaymentController::class, 'paymentPage'])->name('payments.page');
    Route::post('/payment/stripe', [\App\Http\Controllers\PaymentController::class, 'processStripe'])->name('payments.processStripe');
    Route::post('/payment/paytabs', [\App\Http\Controllers\PaymentController::class, 'processPayTabs'])->name('payments.processPayTabs');
    Route::get('/payment/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payments.success');
});

/** Admin */
Route::prefix('admin')->middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\UserAdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [\App\Http\Controllers\Admin\UserAdminController::class, 'listUsers'])->name('admin.users');
    Route::post('/users/toggle/{id}', [\App\Http\Controllers\Admin\UserAdminController::class, 'toggleUserStatus'])->name('admin.users.toggle');
    Route::get('/events', [\App\Http\Controllers\Admin\UserAdminController::class, 'listEvents'])->name('admin.events');
    Route::get('/certificates', [\App\Http\Controllers\Admin\UserAdminController::class, 'listCertificates'])->name('admin.certificates');
    Route::post('/backup', [\App\Http\Controllers\Admin\UserAdminController::class, 'backup'])->name('admin.backup');
});

Auth::routes();

/** PUBLIC: verify + gallery (single, closed block) */
Route::match(['GET', 'POST'], '/verify', function (Request $r) {
    $code = trim($r->input('code', ''));
    $result = null;
    if ($code !== '') {
        $result = DB::table('certificates')->where('verification_code', $code)->first();
    }
    return view('public.verify', compact('result', 'code'));
})->name('verify');

Route::get('/gallery', fn () => view('public.gallery'))->name('gallery');

/** Profile routes (preferred) */
Route::middleware(['web','auth'])->group(function () {
    Route::get('/profile', \App\Http\Controllers\Volunteer\ProfileIndexAction::class)->name('profile');
    Route::get('/volunteer/profile', \App\Http\Controllers\Volunteer\ProfileIndexAction::class)->name('volunteer.profile');
});

/** Avatar upload/remove */
Route::middleware(['web','auth'])->group(function () {
    Route::post('/profile/avatar', [\App\Http\Controllers\Volunteer\AvatarController::class,'store'])->name('profile.avatar.store');
    Route::delete('/profile/avatar', [\App\Http\Controllers\Volunteer\AvatarController::class,'destroy'])->name('profile.avatar.destroy');
});

/** Keep public opportunities in a single file (avoids duplication) */
require __DIR__.'/_opportunities.php';

/** Friendly /signin router + alias (no controller dependency) */
Route::get('/signin', function (\Illuminate\Http\Request $r) {
    $type = strtolower($r->query('type',''));
    if (in_array($type, ['organization','organisation','org'])) {
        return redirect()->to('/login?type=organization');
    }
    return redirect()->to('/login?type=volunteer');
})->name('signin');

Route::get('/sign-in', fn() => redirect()->route('signin'));
require __DIR__.'/_qr_public.php';

require __DIR__.'/_org_auth.php';

require __DIR__.'/_scan.php';

require __DIR__.'/_admin_hours.php';

if (file_exists(__DIR__.'/_admin_aliases.php')) require __DIR__.'/_admin_aliases.php';

require __DIR__.'/_admin_diag.php';

require __DIR__.'/_admin_dashboard_override.php';

require __DIR__.'/_admin_dashboard_compat.php';

require __DIR__.'/_admin_route_aliases.php';

require __DIR__.'/_admin_users_fix.php';

require __DIR__.'/_org_register_fix.php';

require __DIR__.'/_admin_auth_fix.php';

require __DIR__.'/_auth_canonical.php';
