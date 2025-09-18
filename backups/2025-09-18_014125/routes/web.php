Route::redirect('/dashboard','/')->name('dashboard');




/* === THEMED_AUTH_VIEWS_BEGIN (public) === */
Route::middleware(['web','guest'])->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');           // volunteer
    Route::get('/register', fn() => view('auth.register'))->name('register');  // volunteer
});
Route::middleware(['web','guest'])->group(function () {
    Route::get('/org/login', fn() => view('org.auth.login'))->name('org.login');
    Route::get('/org/register', fn() => view('org.auth.register'))->name('org.register');
});
/* === THEMED_AUTH_VIEWS_END === */

// fallback public register hotfix (2025-09-17)
Route::get('/register', function () { return view('auth.register'); });
Route::get('/org/register', function () { return view('org.auth.register'); });
