use Illuminate\Support\Facades\Route;

/**
 * Precedence overrides (GET only) for public pages.
 * These must load BEFORE other web routes to take effect.
 * No POST/logic changes here.
 */

Route::get('/stories', function () {
    return view()->exists('public.stories') ? view('public.stories') : abort(404);
});

Route::get('/opportunities', function () {
    return view()->exists('public.opportunities') ? view('public.opportunities') : abort(404);
});

Route::get('/organizations', function () {
    return view()->exists('public.organizations') ? view('public.organizations') : abort(404);
});

Route::get('/org/login', function () {
    foreach (['org.login','org.auth.login','auth.org_login'] as $v) {
        if (view()->exists($v)) return view($v);
    }
    return redirect('/login');
});
