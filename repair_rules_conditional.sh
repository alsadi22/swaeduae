#!/usr/bin/env bash
set -euo pipefail
APP_ROOT="${APP_ROOT:-$( [ -f artisan ] && echo . || echo laravel-app )}"
cd "$APP_ROOT"

echo "== Backup routes/web.php =="
cp -a routes/web.php routes/web.php.condfix.$(date +%F-%H%M%S).bak

echo "== Remove any existing SwaedUAE rules block =="
perl -0777 -pe "s|// === SwaedUAE RULES BLOCK.*?// === /SwaedUAE RULES BLOCK ===\\s*||s" -i routes/web.php

echo "== Append conditional SwaedUAE rules block =="
cat >> routes/web.php <<'PHP'
// === SwaedUAE RULES BLOCK (conditional) ===
/*
 We only register a shim if the named route does NOT already exist.
 This avoids "Another route has already been assigned name [...]" errors.
*/

if (!Route::has('home'))                 Route::redirect('/r/home','/')->name('home');
if (!Route::has('about'))                Route::redirect('/r/about','/about')->name('about');
if (!Route::has('contact'))              Route::redirect('/r/contact','/contact')->name('contact');
if (!Route::has('faq'))                  Route::redirect('/r/faq','/faq')->name('faq');
if (!Route::has('partners'))             Route::redirect('/r/partners','/partners')->name('partners');

if (!Route::has('opportunities.index'))  Route::redirect('/r/opportunities','/opportunities')->name('opportunities.index');
if (!Route::has('opportunities.show')) {
    Route::get('/r/opportunities/{id}', function ($id) { return redirect('/opportunities/'.$id); })
         ->name('opportunities.show');
}

if (!Route::has('volunteer.dashboard'))  Route::redirect('/r/volunteer/dashboard','/volunteer/dashboard')->name('volunteer.dashboard');
if (!Route::has('volunteer.profile'))    Route::redirect('/r/volunteer/profile','/volunteer/profile')->name('volunteer.profile');

if (!Route::has('admin.users'))          Route::redirect('/r/admin/users','/admin/users')->name('admin.users');
if (!Route::has('admin.events'))         Route::redirect('/r/admin/events','/admin/events')->name('admin.events');
if (!Route::has('admin.certificates'))   Route::redirect('/r/admin/certificates','/admin/certificates')->name('admin.certificates');
if (!Route::has('admin.kyc'))            Route::redirect('/r/admin/kyc','/admin')->name('admin.kyc'); // temp target if not ready

// QR alias is safe to (re)define; keep name stable
Route::get('/qr/verify', function () { return redirect()->route('verify'); })->name('qr.verify');

// Language toggle: only add if name missing
if (!Route::has('lang.switch')) {
    Route::post('/lang/toggle', function () {
        $current = app()->getLocale();
        $target  = request()->input('locale', $current === 'ar' ? 'en' : 'ar');
        session(['locale' => $target]);
        app()->setLocale($target);
        return back();
    })->name('lang.switch');
}
// === /SwaedUAE RULES BLOCK ===
PHP

echo "== Clear & cache routes =="
php artisan route:clear >/dev/null 2>&1 || true
php artisan route:cache

echo "== Quick verify: show a few names =="
php artisan route:list | grep -E ' name | home | faq | about | contact | partners | opportunities\.index | opportunities\.show | volunteer\.dashboard | volunteer\.profile | admin\.(users|events|certificates|kyc) | lang\.switch ' -n || true

echo "== QR alias check =="
echo -n "/qr/verify -> " && curl -skI https://swaeduae.ae/qr/verify | awk 'NR==1{print $2}'
