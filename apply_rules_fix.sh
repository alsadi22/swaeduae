#!/usr/bin/env bash
set -euo pipefail

APP_ROOT="${APP_ROOT:-$( [ -f artisan ] && echo . || echo laravel-app )}"
cd "$APP_ROOT"

echo "== Backing up routes/web.php =="
cp -a routes/web.php routes/web.php.$(date +%F-%H%M%S).bak

echo "== Inserting SwaedUAE RULES BLOCK (idempotent) =="
if ! grep -q 'SwaedUAE RULES BLOCK' routes/web.php; then
cat >> routes/web.php <<'PHP'
// === SwaedUAE RULES BLOCK v2025-08-23 (do not remove) ===
use Illuminate\Support\Facades\Route;

# Named route shims that redirect to existing URLs (no handler conflicts)
Route::redirect('/r/home', '/')->name('home');
Route::redirect('/r/about', '/about')->name('about');
Route::redirect('/r/contact', '/contact')->name('contact');
Route::redirect('/r/faq', '/faq')->name('faq');
Route::redirect('/r/partners', '/partners')->name('partners');

Route::redirect('/r/opportunities', '/opportunities')->name('opportunities.index');
Route::get('/r/opportunities/{id}', fn ($id) => redirect('/opportunities/'.$id))->name('opportunities.show');

Route::redirect('/r/volunteer/dashboard', '/volunteer/dashboard')->name('volunteer.dashboard');
Route::redirect('/r/volunteer/profile', '/volunteer/profile')->name('volunteer.profile');

Route::redirect('/r/admin/users', '/admin/users')->name('admin.users');
Route::redirect('/r/admin/events', '/admin/events')->name('admin.events');
Route::redirect('/r/admin/certificates', '/admin/certificates')->name('admin.certificates');
# If KYC page not ready yet, point to admin home temporarily
Route::redirect('/r/admin/kyc', '/admin')->name('admin.kyc');

# QR alias to public verification
Route::get('/qr/verify', fn () => redirect()->route('verify'))->name('qr.verify');

# Language toggle endpoint (named), independent of any existing route
Route::post('/lang/toggle', function (\Illuminate\Http\Request $request) {
    $current = app()->getLocale();
    $target  = $request->input('locale', $current === 'ar' ? 'en' : 'ar');
    session(['locale' => $target]);
    app()->setLocale($target);
    return back();
})->name('lang.switch');
// === /SwaedUAE RULES BLOCK ===
PHP
else
  echo "Rules block already present — skipping insert."
fi

echo "== Ensure missing Blade files exist =="
mkdir -p resources/views/{layouts,components}
[ -f resources/views/layouts/guest.blade.php ] || cat > resources/views/layouts/guest.blade.php <<'BLADE'
<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $title ?? config('app.name') }}</title></head>
<body class="guest">@yield('content')</body></html>
BLADE

[ -f resources/views/components/lang-toggle.blade.php ] || cat > resources/views/components/lang-toggle.blade.php <<'BLADE'
<form method="POST" action="{{ route('lang.switch') }}">
  @csrf
  <button type="submit" name="locale" value="{{ app()->getLocale()==='ar'?'en':'ar' }}">
    {{ app()->getLocale()==='ar' ? 'English' : 'العربية' }}
  </button>
</form>
BLADE

echo "== Clear & cache routes =="
php artisan route:clear >/dev/null 2>&1 || true
php artisan route:cache || true

echo "== Re-check (named routes, QR alias, blades) =="
php artisan route:list | awk -v FS='[|]' '
/^\|/ {
  for (i=1;i<=NF;i++) gsub(/^[ \t]+|[ \t]+$/, "", $i);
  if ($2=="Method") next;
  print $2 "\t" $3 "\t" $4 "\t" $6
}' > /tmp/routes.tsv

required="home faq about contact partners opportunities.index opportunities.show volunteer.dashboard volunteer.profile admin.users admin.events admin.certificates admin.kyc lang.switch"
cut -f3 /tmp/routes.tsv | sort -u > /tmp/route_names.txt
echo "Missing named routes (after apply):"
miss=0
for n in $required; do
  if ! grep -Fxq "$n" /tmp/route_names.txt; then echo " - $n"; miss=1; fi
done
[ $miss -eq 0 ] && echo " (none) ✅"

echo -n "/qr/verify -> " && curl -skI https://swaeduae.ae/qr/verify | awk 'NR==1{print $2}'

for f in resources/views/layouts/guest.blade.php resources/views/components/lang-toggle.blade.php; do
  [ -f "$f" ] && echo "OK  $f" || echo "MISS $f"
done

echo "== Done. If any items still show as missing, share the outputs above =="
