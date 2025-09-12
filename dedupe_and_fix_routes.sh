#!/usr/bin/env bash
set -euo pipefail
APP_ROOT="${APP_ROOT:-$( [ -f artisan ] && echo . || echo laravel-app )}"
cd "$APP_ROOT"

ts(){ date +%F-%H%M%S; }

echo "== Backup routes/web.php =="
cp -a routes/web.php routes/web.php.dedupe.$(ts).bak

echo "== Remove any previous SwaedUAE blocks =="
perl -0777 -pe "s|// === SwaedUAE RULES BLOCK.*?// === /SwaedUAE RULES BLOCK ===\\s*||sg" -i routes/web.php

echo "== Remove leftover r/* shims that cause duplicate names (home/faq/partners/admin certs/users) =="
# Safe targeted removals even if formatting varies:
perl -0777 -pe "s|\\s*Route::redirect\\('/r/home'.*?;\\s*||sg" -i routes/web.php
perl -0777 -pe "s|\\s*Route::redirect\\('/r/faq'.*?;\\s*||sg" -i routes/web.php
perl -0777 -pe "s|\\s*Route::redirect\\('/r/partners'.*?;\\s*||sg" -i routes/web.php
perl -0777 -pe "s|\\s*Route::redirect\\('/r/admin/certificates'.*?;\\s*||sg" -i routes/web.php
perl -0777 -pe "s|\\s*Route::redirect\\('/r/admin/users'.*?;\\s*||sg" -i routes/web.php

echo "== Build current route name list (no cache) =="
php artisan route:clear >/dev/null 2>&1 || true
php artisan route:list | awk -v FS='[|]' '
/^\|/ {
  for (i=1;i<=NF;i++) gsub(/^[ \t]+|[ \t]+$/, "", $i);
  if ($2=="Method") next;
  print $2 "\t" $3 "\t" $4 "\t" $6
}' > /tmp/routes.tsv

cut -f3 /tmp/routes.tsv | sort -u > /tmp/route_names.txt

required=(home faq about contact partners opportunities.index opportunities.show volunteer.dashboard volunteer.profile admin.users admin.events admin.certificates admin.kyc lang.switch)
missing=()
for n in "${required[@]}"; do
  if ! grep -Fxq "$n" /tmp/route_names.txt; then missing+=("$n"); fi
done

echo "Missing names detected: ${missing[*]:-(none)}"

echo "== Append a fresh SwaedUAE rules block with ONLY missing names =="
{
  echo "// === SwaedUAE RULES BLOCK (auto-generated $(ts)) ==="
  for n in "${missing[@]}"; do
    case "$n" in
      home)                echo "Route::redirect('/r/home','/')->name('home');";;
      faq)                 echo "Route::redirect('/r/faq','/faq')->name('faq');";;
      about)               echo "Route::redirect('/r/about','/about')->name('about');";;
      contact)             echo "Route::redirect('/r/contact','/contact')->name('contact');";;
      partners)            echo "Route::redirect('/r/partners','/partners')->name('partners');";;
      opportunities.index) echo "Route::redirect('/r/opportunities','/opportunities')->name('opportunities.index');";;
      opportunities.show)  echo "Route::get('/r/opportunities/{id}', function(\$id){ return redirect('/opportunities/'.\$id); })->name('opportunities.show');";;
      volunteer.dashboard) echo "Route::redirect('/r/volunteer/dashboard','/volunteer/dashboard')->name('volunteer.dashboard');";;
      volunteer.profile)   echo "Route::redirect('/r/volunteer/profile','/volunteer/profile')->name('volunteer.profile');";;
      admin.users)         echo "Route::redirect('/r/admin/users','/admin/users')->name('admin.users');";;
      admin.events)        echo "Route::redirect('/r/admin/events','/admin/events')->name('admin.events');";;
      admin.certificates)  echo "Route::redirect('/r/admin/certificates','/admin/certificates')->name('admin.certificates');";;
      admin.kyc)           echo "Route::redirect('/r/admin/kyc','/admin')->name('admin.kyc');";;
      lang.switch)         echo "Route::post('/lang/toggle', function(){ \$cur=app()->getLocale(); \$t=request()->input('locale', \$cur==='ar'?'en':'ar'); session(['locale'=>\$t]); app()->setLocale(\$t); return back(); })->name('lang.switch');";;
    esac
  done
  # Always ensure QR alias exists (won't conflict on name)
  echo "Route::get('/qr/verify', function(){ return redirect()->route('verify'); })->name('qr.verify');"
  echo "// === /SwaedUAE RULES BLOCK ==="
} >> routes/web.php

echo "== Cache routes =="
php artisan route:cache

echo "== Verify the names (post-cache) =="
php artisan route:list | grep -E ' home | faq | about | contact | partners | opportunities\.index | opportunities\.show | volunteer\.dashboard | volunteer\.profile | admin\.(users|events|certificates|kyc) | lang\.switch ' -n || true

echo "== QR alias check =="
echo -n "/qr/verify -> " && curl -skI https://swaeduae.ae/qr/verify | awk 'NR==1{print $2}'
