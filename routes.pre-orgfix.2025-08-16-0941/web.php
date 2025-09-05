<?php





/* Fallback logout route (safe if auth scaffolding not present) */
if (!Route::has('logout')) {
    Route::middleware(['web'])->match(['GET','POST'], '/logout', function (\Illuminate\Http\Request $request) {
        if (auth()->check()) { \Illuminate\Support\Facades\Auth::logout(); }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
}

/* Org login landing (redirects to regular /login UI) */
Route::middleware(['web','guest'])
    ->get('/org/login', function () {
        return redirect('/login');
    })
    ->name('org.login');

/* Org login entry -> forward to canonical /login */
Route::middleware(['web','guest'])->get('/org/login', function () {
    return redirect('/login');
})->name('org.login');

/* Org login entry -> forward to canonical /login */
Route::middleware(['web','guest'])->get('/org/login', function () {
    return redirect('/login');
})->name('org.login');

/* Minimal Home route so / stops 404ing */
Route::middleware(['web'])->get('/', function () {
    if (view()->exists('home')) return view('home');
    if (view()->exists('welcome')) return view('welcome');
    return response('OK', 200);
})->name('home');

/* Org login entry -> forward to canonical /login */
Route::middleware(['web','guest'])->get('/org/login', function () {
    return redirect('/login');
})->name('org.login');
