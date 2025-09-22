<?php

// Ensure static route beats events/{idOrSlug}
Route::get('/events/browse',[\App\Http\Controllers\Public\EventsBrowseController::class,'index'])->name('events.browse');

use App\Http\Controllers\Org\OpportunityController;
use App\Http\Controllers\Org\ApplicationReviewController;
use App\Http\Controllers\Public\EventsBrowseController;
use App\Http\Controllers\Public\ContactController;
if (file_exists(__DIR__.'/_admin_auth.php')) require __DIR__.'/_admin_auth.php';
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

Route::get('/healthz', fn () => response('ok', 200))->name('health');

/** Public site */
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
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
if (file_exists(__DIR__.'/_opportunities.php')) require __DIR__.'/_opportunities.php';

/** Friendly /signin router + alias (no controller dependency) */
Route::get('/signin', function (\Illuminate\Http\Request $r) {
    $type = strtolower($r->query('type',''));
    if (in_array($type, ['organization','organisation','org'])) {
        return redirect()->to('/login?type=organization');
    }
    return redirect()->to('/login?type=volunteer');
})->name('signin');

Route::get('/sign-in', fn() => redirect()->route('signin'));
if (file_exists(__DIR__.'/_qr_public.php')) require __DIR__.'/_qr_public.php';


if (file_exists(__DIR__.'/_scan.php')) require __DIR__.'/_scan.php';

if (file_exists(__DIR__.'/_admin_hours.php')) require __DIR__.'/_admin_hours.php';

if (file_exists(__DIR__.'/_admin_aliases.php')) require __DIR__.'/_admin_aliases.php';

if (file_exists(__DIR__.'/_admin_diag.php')) require __DIR__.'/_admin_diag.php';

if (file_exists(__DIR__.'/_admin_dashboard_override.php')) require __DIR__.'/_admin_dashboard_override.php';

if (file_exists(__DIR__.'/_admin_dashboard_compat.php')) require __DIR__.'/_admin_dashboard_compat.php';

if (file_exists(__DIR__.'/_admin_route_aliases.php')) require __DIR__.'/_admin_route_aliases.php';

if (file_exists(__DIR__.'/_admin_users_fix.php')) require __DIR__.'/_admin_users_fix.php';

if (file_exists(__DIR__.'/_org_register_fix.php')) require __DIR__.'/_org_register_fix.php';

if (file_exists(__DIR__.'/_admin_auth_fix.php')) require __DIR__.'/_admin_auth_fix.php';

if (file_exists(__DIR__.'/_auth_canonical.php')) require __DIR__.'/_auth_canonical.php';
if (file_exists(__DIR__.'/_auth_aliases.php')) require __DIR__.'/_auth_aliases.php';

// include i18n if present
if (file_exists(__DIR__.'/_i18n.php')) require __DIR__.'/_i18n.php';

// Injected: auth POST overrides
// Injected: admin auth POST overrides
use App\Http\Controllers\SitemapController;
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::view('/about', 'pages.about')->name('pages.about')->middleware(\App\Http\Middleware\SetLocale::class);
Route::view('/privacy', 'pages.privacy')->name('pages.privacy')->middleware(\App\Http\Middleware\SetLocale::class);
Route::view('/terms', 'pages.terms')->name('pages.terms')->middleware(\App\Http\Middleware\SetLocale::class);
use App\Http\Controllers\LangController;
/* removed duplicate /locale/{locale} route */

// Fallback org logout (POST only), guarded by auth:org
use Illuminate\Support\Facades\Auth;
Route::post('/org/logout', function (Request $request) {
    Auth::guard('org')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware(['auth:org'])->name('org.logout');
if (file_exists(base_path('routes/events.php'))) {
    require base_path('routes/events.php');
}

/* === Notifications (P9) === */
use App\Http\Controllers\NotificationController;

Route::middleware(['web','auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/notifications/test', [NotificationController::class, 'test'])->name('notifications.test');
});

/* === P12: Canonical 301 redirects (safe + minimal) === */

Route::permanentRedirect('/dashboard', '/profile')->name('redir.dashboard.profile');
Route::permanentRedirect('/home', '/')->name('redir.home.root');
Route::get('/volunteer/profile', fn() => redirect('/profile', 301))->name('redir.volunteer.profile');
Route::get('/volunteer/dashboard', fn() => redirect('/profile', 301))->name('redir.volunteer.dashboard');
Route::get('/contact', function () {
    foreach (['contact','pages.contact','argon_front.contact','frontend.contact'] as $v) {
        if (view()->exists($v)) return view($v);
    }
    return response('Contact page coming soon.', 200);
})->name('contact.show');

// ---- compat: organizations.register (keeps old blades working) ----
Route::get('/organizations/register', function () {
    // TODO: point this to your real controller/view when ready
    return redirect()->to('/register?type=organization');
})->name('organizations.register');
Route::post('/organizations/register', function () {
    return redirect()->to('/register?type=organization');
})->name('organizations.register.store');

/* --- Phase 1: observability healthz --- */
\Illuminate\Support\Facades\Route::get('/healthz', function () {
    $okDb = true;
    try { \Illuminate\Support\Facades\DB::select('select 1'); }
    catch (\Throwable $e) { $okDb = false; }

    $queuePending = \Illuminate\Support\Facades\Schema::hasTable('jobs')
        ? \Illuminate\Support\Facades\DB::table('jobs')->count()
        : null;
    $queueFailed = \Illuminate\Support\Facades\Schema::hasTable('failed_jobs')
        ? \Illuminate\Support\Facades\DB::table('failed_jobs')->count()
        : null;

    return response()->json([
        'ok'             => $okDb,
        'time'           => now()->toIso8601String(),
        'app_env'        => config('app.env'),
        'cache'          => config('cache.default'),
        'queue'          => config('queue.default'),
        'queue_pending'  => $queuePending,
        'queue_failed'   => $queueFailed,
    ], 200, ['Cache-Control' => 'no-store']);
})->name('healthz');
Route::middleware(['web','auth','throttle:30,1'])->prefix('admin')->group(function () {
  Route::get('/_diag', function () {
    $now = now();
    $storage = storage_path();
    $free  = @disk_free_space($storage);
    $total = @disk_total_space($storage);
    $usedPct = $total ? round(100 * ($total - $free) / $total, 1) : null;

    $dbOk = false;
    try { \Illuminate\Support\Facades\DB::select('select 1'); $dbOk = true; } catch (\Throwable $e) {}

    $data = [
      'ok'   => true,
      'ts'   => $now->toIso8601String(),
      'app'  => [
        'env'     => config('app.env'),
        'debug'   => (bool) config('app.debug'),
        'laravel' => app()->version(),
        'php'     => PHP_VERSION,
      ],
      'cache' => config('cache.default'),
      'queue' => [
        'driver'  => config('queue.default'),
        'pending' => (int) \Illuminate\Support\Facades\DB::table('jobs')->count(),
        'failed'  => (int) \Illuminate\Support\Facades\DB::table('failed_jobs')->count(),
      ],
      'db'   => ['ok' => $dbOk],
      'disk' => [
        'path'         => $storage,
        'total_bytes'  => $total,
        'free_bytes'   => $free,
        'used_percent' => $usedPct,
      ],
    ];
    return response()->json($data)->header('Cache-Control','no-store');
  })->name('admin.diag');
});
Route::get('/health', function () {
    return response()->json([
        'ok'   => true,
        'time' => now()->toDateTimeString(),
        'env'  => app()->environment(),
    ]);
})->name('health');
Route::get('/health', function () {
    return response()->json([
        'ok'   => true,
        'time' => now()->toDateTimeString(),
        'env'  => app()->environment(),
    ]);
})->name('health');

// Contact routes (Wave1)
Route::get("/contact", [\App\Http\Controllers\Public\ContactController::class, "show"])->name("contact.show");
Route::post("/contact", [\App\Http\Controllers\Public\ContactController::class, "send"])->middleware(["throttle:forms"])->name("contact.submit");


// Public events browse (final)
Route::get(
    [\App\Http\Controllers\Public\EventsBrowseController::class, 'index']
)->name('events.browse');

