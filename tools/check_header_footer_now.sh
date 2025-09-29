#!/usr/bin/env bash
set -euo pipefail
echo "=== ROUTE SERVING '/' ==="
php artisan route:list --path=/ --method=GET || true

echo -e "\n=== HomeController@index (return view line) ==="
nl -ba app/Http/Controllers/HomeController.php | sed -n '1,200p' | awk '/function +index\(/, /}/'

echo -e "\n=== home.blade.php extends + content section ==="
[ -f resources/views/home.blade.php ] && {
  grep -n "^@extends" resources/views/home.blade.php || true
  grep -n "@section('content')" resources/views/home.blade.php || true
}

echo -e "\n=== layouts/app.blade.php quick summary ==="
if [ -f resources/views/layouts/app.blade.php ]; then
  echo -n "size="; wc -c < resources/views/layouts/app.blade.php
  echo "-- first 40 lines --"
  nl -ba resources/views/layouts/app.blade.php | sed -n '1,40p'
  echo "-- include lines --"
  grep -n "@include" resources/views/layouts/app.blade.php || true
  echo "-- has yield('content')? --"
  grep -n "@yield('content')" resources/views/layouts/app.blade.php || echo "(no @yield('content'))"
  echo "-- mentions navbar/footer partials? --"
  grep -n -E "navbar|_navbar|footer|_footer" resources/views/layouts/app.blade.php || echo "(no navbar/footer includes found)"
else
  echo "layouts/app.blade.php missing!"
fi

echo -e "\n=== Do the partials exist anywhere? ==="
grep -RIn --include='*.blade.php' -E "_navbar|navbar|_footer|footer" resources/views || true

echo -e "\n=== LIVE HTML (unauthenticated) sanity ==="
echo -n "Argon CSS link: "; curl -sk https://swaeduae.ae/ | grep -m1 -n 'argon-dashboard\.min\.css' || echo "MISSING"
echo -n "<nav> tag: ";      curl -sk https://swaeduae.ae/ | grep -m1 -o '<nav[^>]*>' || echo "MISSING"
echo -n "<footer> tag: ";   curl -sk https://swaeduae.ae/ | grep -m1 -o '<footer[^>]*>' || echo "MISSING"

echo -e "\n=== Any backups of the layout to restore from? ==="
ls -1t resources/views/layouts/app.blade.php.bak_* 2>/dev/null | head -n5 || echo "(no backups found)"

echo -e "\n(If navbar/footer are missing in the layout, that’s the cause.)"
