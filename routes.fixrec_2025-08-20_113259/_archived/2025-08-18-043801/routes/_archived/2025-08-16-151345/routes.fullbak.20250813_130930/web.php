<?php
use App\Http\Controllers\Volunteer\ProfileController;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Public routes with the exact names used by your Blade views
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view()->exists('welcome') ? view('welcome') : redirect('/opportunities');
})->name('home');

/* Opportunities (public.opportunities, public.opportunities.show) */
Route::get('/opportunities', function () {
    if (class_exists(\App\Http\Controllers\OpportunityController::class)) {
        return app(\App\Http\Controllers\OpportunityController::class)->index(request());
    }
    $opportunities = DB::table('opportunities')->orderByDesc('created_at')->paginate(12);
    return view()->exists('opportunities.index')
        ? view('opportunities.index', compact('opportunities'))
        : view('partials._opps_fallback', compact('opportunities'));
})->name('public.opportunities');

Route::get('/opportunities/{id}', function ($id) {
    if (class_exists(\App\Http\Controllers\OpportunityController::class)) {
        return app(\App\Http\Controllers\OpportunityController::class)->show($id);
    }
    $o = DB::table('opportunities')->where('id', $id)->first();
    abort_if(!$o, 404);
    return view()->exists('opportunities.show')
        ? view('opportunities.show', ['opportunity' => $o])
        : response()->view('layouts.app', ['slot' => "<div class='container py-4'><h1>{$o->title}</h1><p>{$o->description}</p></div>"]);
})->whereNumber('id')->name('public.opportunities.show');

/* Events (public.events, public.events.show) */
Route::get('/events', function () {
    if (class_exists(\App\Http\Controllers\EventController::class)) {
        return app(\App\Http\Controllers\EventController::class)->index(request());
    }
    $events = DB::table('events')->orderByDesc('start_at')->limit(12)->get();
    return view()->exists('events.index')
        ? view('events.index', compact('events'))
        : view('partials._events_fallback', compact('events'));
})->name('public.events');

Route::get('/events/{id}', function ($id) {
    if (class_exists(\App\Http\Controllers\EventController::class)) {
        return app(\App\Http\Controllers\EventController::class)->show($id);
    }
    $e = DB::table('events')->where('id', $id)->first();
    abort_if(!$e, 404);
    return view()->exists('events.show')
        ? view('events.show', ['event' => $e])
        : response()->view('layouts.app', ['slot' => "<div class='container py-4'><h1>{$e->title}</h1><p>{$e->description}</p></div>"]);
})->whereNumber('id')->name('public.events.show');

/* Organizations & Gallery (public.organizations, public.gallery) */
Route::get('/organizations', function () {
    return view()->exists('pages.organizations')
        ? view('pages.organizations')
        : view('welcome')->with('message', __('Organizations page coming soon.'));
})->name('public.organizations');

Route::get('/gallery', function () {
    return view()->exists('pages.gallery')
        ? view('pages.gallery')
        : view('welcome')->with('message', __('Gallery page coming soon.'));
})->name('public.gallery');

/*
|--------------------------------------------------------------------------
| Optional extras (only if files exist)
|--------------------------------------------------------------------------
*/
foreach (['swaed_extras.php', 'attendance.php', 'seo.php'] as $f) {
    $p = __DIR__.'/'.$f;
    if (file_exists($p)) require $p;
}

/*
|--------------------------------------------------------------------------
| Admin routes (already middleware-protected inside routes/admin.php)
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__.'/admin.php')) {
    require __DIR__.'/admin.php';
}

/*
|--------------------------------------------------------------------------
| Auth + simple public pages (added)
|--------------------------------------------------------------------------
*/

# 1) Logout route (GET + POST for compatibility with existing <a href="{{ route('logout') }}">)
Route::match(['GET','POST'], '/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/')->with('status', __('Logged out'));
})->name('logout.web1');

# 2) Partners page (public.partners)
Route::get('/partners', function () {
    return view()->exists('pages.partners')
        ? view('pages.partners')
        : view('welcome')->with('message', __('Partners page coming soon.'));
})->name('public.partners');

# 3) Categories page (public.categories)
Route::get('/categories', function () {
    if (class_exists(\App\Http\Controllers\CategoryController::class)) {
        return app(\App\Http\Controllers\CategoryController::class)->index();
    }
    return view()->exists('pages.categories')
        ? view('pages.categories')
        : view('welcome')->with('message', __('Categories page coming soon.'));
})->name('public.categories');

// Include auth_public (logout, partners, categories)
if (file_exists(__DIR__.'/auth_public.php')) {
    require __DIR__.'/auth_public.php';
}

/*
|--------------------------------------------------------------------------
| Auth routes (ensure named 'login' exists for auth middleware)
|--------------------------------------------------------------------------
*/
if (!\Illuminate\Support\Facades\Route::has('login')) {
    \Illuminate\Support\Facades\Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    \Illuminate\Support\Facades\Route::post('/login', function (\Illuminate\Http\Request $request) {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]->middleware(['honeypot','throttle:login']));

        if (\Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = \Illuminate\Support\Facades\Auth::user();
            $dest = '/';
            if ($user && (
                (!empty($user->is_admin) && $user->is_admin) ||
                (method_exists($user, 'hasRole') && $user->hasRole('admin'))
            )) {
                $dest = route('admin.dashboard');
            }
            return redirect()->intended($dest);
        }

        return back()->withErrors(['email' => __('Invalid credentials')])->onlyInput('email');
    })->name('login.attempt');
}

/* Keep a named logout (if still missing) */
if (!\Illuminate\Support\Facades\Route::has('logout')) {
    \Illuminate\Support\Facades\Route::match(['GET','POST'], '/logout', function (\Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('status', __('Logged out'));
    })->name('logout.web2');
}

/* Make sure partners & categories named routes exist */
if (!\Illuminate\Support\Facades\Route::has('public.partners')) {
    \Illuminate\Support\Facades\Route::get('/partners', function () {
        return view()->exists('pages.partners')
            ? view('pages.partners')
            : view('welcome')->with('message', __('Partners page coming soon.'));
    })->name('public.partners');
}
if (!\Illuminate\Support\Facades\Route::has('public.categories')) {
    \Illuminate\Support\Facades\Route::get('/categories', function () {
        if (class_exists(\App\Http\Controllers\CategoryController::class)) {
            return app(\App\Http\Controllers\CategoryController::class)->index();
        }
        return view()->exists('pages.categories')
            ? view('pages.categories')
            : view('welcome')->with('message', __('Categories page coming soon.'));
    })->name('public.categories');
}

// Autoload extra admin named routes (settings alias, media, exportCsv)
if (file_exists(__DIR__.'/admin_extras.php')) {
    require_once __DIR__.'/admin_extras.php';
}

// Load extra admin named routes (media, organizations export CSV)
if (file_exists(__DIR__.'/admin_extras.php')) {
    require_once __DIR__.'/admin_extras.php';
}

// Redirect /admin to /admin/dashboard
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
});

// --- Admin bootstrap (idempotent) ---
if (file_exists(__DIR__.'/admin.php')) {
    require_once __DIR__.'/admin.php';
}

// If the named route admin.dashboard isn't registered, define a safe fallback.
if (!\Illuminate\Support\Facades\Route::has('admin.dashboard')) {
    \Illuminate\Support\Facades\Route::prefix('admin')
        ->name('admin.')
        ->middleware(['web','auth', \App\Http\Middleware\AdminMiddleware::class])
        ->group(function () {
            \Illuminate\Support\Facades\Route::get('/dashboard', function () {
                return view()->exists('admin.dashboard')
                    ? view('admin.dashboard')
                    : view('admin.placeholder', ['title' => 'Dashboard']);
            })->name('dashboard');
        });
}

// Redirect /admin to /admin/dashboard without named route dependency
\Illuminate\Support\Facades\Route::get('/admin', function () {
    return redirect('/admin/dashboard');
});

// Load admin routes
require __DIR__ . '/admin.php';

use App\Http\Controllers\Admin\CertificateController;
Route::get('/verify/{code}', [CertificateController::class, 'verify'])->name('verify.certificate');


// --- SawaedUAE additions: Volunteer profile & learning ---
Route::middleware(['web','auth'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/learning/request', [\App\Http\Controllers\LearningRequestController::class, 'create'])->name('learning.create');
    Route::post('/learning/request', [\App\Http\Controllers\LearningRequestController::class, 'store'])->name('learning.store');
});

// Sign-in options (UAE PASS / Apple / Google stubs)
Route::get('/signin', function(){ return view('auth.signin-options'); })->name('signin.options');

// --- SawaedUAE additions: Volunteer profile & learning ---
Route::middleware(['web','auth'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/learning/request', [\App\Http\Controllers\LearningRequestController::class, 'create'])->name('learning.create');
    Route::post('/learning/request', [\App\Http\Controllers\LearningRequestController::class, 'store'])->name('learning.store');
});

// Sign-in options (UAE PASS / Apple / Google stubs)
Route::get('/signin', function(){ return view('auth.signin-options'); })->name('signin.options');

// --- SawaedUAE: QR attendance
Route::middleware(['web','auth'])->group(function () {
    Route::get('/a/{token}', [\App\Http\Controllers\Public\AttendanceController::class, 'handle'])->name('attendance.token');
});

// --- SawaedUAE: Volunteer certificates & transcript ---
Route::middleware(['web','auth'])->group(function () {
    Route::get('/my/certificates', [\App\Http\Controllers\CertificateController::class, 'my'])->name('certificates.my');
    Route::get('/my/certificates/{uuid}.pdf', [\App\Http\Controllers\CertificateController::class, 'pdf'])->name('certificates.pdf');
    Route::get('/my/transcript.pdf', [\App\Http\Controllers\TranscriptController::class, 'pdf'])->name('transcript.pdf');
});

// --- QR Attendance (public; requires login) ---
Route::middleware(['web'])->group(function () {
    Route::get('/op/checkin/{token}',  [\App\Http\Controllers\AttendanceController::class,'checkin'])->name('op.checkin');
    Route::get('/op/checkout/{token}', [\App\Http\Controllers\AttendanceController::class,'checkout'])->name('op.checkout');
});

// Public homepage & about
Route::get('/', fn()=>view('home'))->name('home');
Route::get('/about', fn()=>view('about'))->name('about.page');

// Public opportunities
Route::get('/opportunities', [\App\Http\Controllers\Public\PublicOpportunityController::class,'index'])->name('opps.public.index');
Route::get('/opportunities/{id}', [\App\Http\Controllers\Public\PublicOpportunityController::class,'show'])->name('opps.public.show');

// Applications (auth+verified)
Route::middleware(['auth','verified'])->group(function(){
  Route::post('/opportunities/{id}/apply', [\App\Http\Controllers\ApplicationController::class,'apply'])->name('apply');
  Route::post('/opportunities/{id}/cancel', [\App\Http\Controllers\ApplicationController::class,'cancel'])->name('apply.cancel');
  Route::get('/dashboard', [\App\Http\Controllers\Volunteer\VolunteerDashboardController::class,'index'])->name('vol.dashboard.alt');
});

// Certificate verify
Route::get('/verify', [\App\Http\Controllers\CertificateVerifyController::class,'form'])->name('cert.verify.form');
Route::get('/verify/{code}', [\App\Http\Controllers\CertificateVerifyController::class,'check'])->name('cert.verify.check');

/* ==== Public auth (login/register) + email verification ==== */
Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\PublicAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\PublicAuthController::class, 'login']->middleware(['honeypot','throttle:login']));
    Route::get('/register', [\App\Http\Controllers\Auth\PublicAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\PublicAuthController::class, 'register']->middleware(['honeypot']))->name('register.perform');
});

Route::post('/logout', [\App\Http\Controllers\Auth\PublicAuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth','signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth','throttle:6,1'])->name('verification.send');


// SPRINT 1 routes include (safe)
if (file_exists(base_path('routes/sprint1.php'))) { require base_path('routes/sprint1.php'); }

// SPRINT 1 compat routes include (safe)
if (file_exists(base_path('routes/sprint1_compat.php'))) { require base_path('routes/sprint1_compat.php'); }

// SPRINT 2 routes include (safe)
if (file_exists(base_path('routes/sprint2.php'))) { require base_path('routes/sprint2.php'); }

// SPRINT 3 routes include (safe)
if (file_exists(base_path('routes/sprint3.php'))) { require base_path('routes/sprint3.php'); }

// SPRINT 4 routes include (safe)
if (file_exists(base_path('routes/sprint4.php'))) { require base_path('routes/sprint4.php'); }

// SPRINT 5 routes include (safe)
if (file_exists(base_path('routes/sprint5.php'))) { require base_path('routes/sprint5.php'); }

// SPRINT 6 routes include (safe)
if (file_exists(base_path('routes/sprint6.php'))) { require base_path('routes/sprint6.php'); }

// SPRINT 7 routes include (safe)
if (file_exists(base_path('routes/sprint7.php'))) { require base_path('routes/sprint7.php'); }

// SPRINT 8 routes include (safe)
if (file_exists(base_path('routes/sprint8.php'))) { require base_path('routes/sprint8.php'); }

Route::middleware(['web','auth','verified'])->group(function () {
    Route::get('/volunteer/profile/{tab?}', [ProfileController::class, 'index'])
        ->where('tab', 'overview|hours|events|applications|certificates')
        ->name('volunteer.profile');
});
