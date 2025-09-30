#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
echo "=== CHECK: HOME & ORG ==="

echo -e "\n[1] routes/web.php — first 20 lines (verify starts with <?php):"
nl -ba routes/web.php | sed -n '1,20p'

echo -e "\n[2] Route row serving '/':"
php artisan route:list --path=/ --method=GET || true

echo -e "\n[3] Where '/' is defined (exact file:line):"
grep -RInE "Route::(get|view)\(\s*['\"]/['\"]" routes || true

echo -e "\n[4] HomeController@index (first 80 lines):"
nl -ba app/Http/Controllers/HomeController.php | sed -n '1,80p'

echo -e "\n[5] home.blade.php — header + presence of @section('content'):"
[ -f resources/views/home.blade.php ] && {
  echo -n "size="; wc -c < resources/views/home.blade.php
  head -n 3 resources/views/home.blade.php
  grep -n "@section('content')" resources/views/home.blade.php || echo "(no @section('content'))"
}

echo -e "\n[6] layouts/app.blade.php — header + presence of @yield('content'):"
[ -f resources/views/layouts/app.blade.php ] && {
  echo -n "size="; wc -c < resources/views/layouts/app.blade.php
  head -n 3 resources/views/layouts/app.blade.php
  grep -n "@yield('content')" resources/views/layouts/app.blade.php || echo "(no @yield('content'))"
  grep -n '<body' resources/views/layouts/app.blade.php || true
}

echo -e "\n[7] Runtime size for '/':"
echo -n "Homepage bytes: "; curl -sk https://swaeduae.ae/ | wc -c

echo -e "\n[8] ORG — route row for /org/dashboard:"
php artisan route:list --path=org/dashboard || true

echo -e "\n[9] ORG — dashboard blades' @extends lines:"
head -n1 resources/views/org/dashboard.blade.php 2>/dev/null || true
head -n1 resources/views/org/dashboard/index.blade.php 2>/dev/null || true

echo -e "\n[10] ORG layout body tag (source vs live):"
grep -n '<body' resources/views/org/layout.blade.php || true
echo -n "Rendered: "; curl -sk https://swaeduae.ae/org/dashboard | grep -m1 -o '<body[^>]*>' || true

echo -e "\n[11] ORG menu CSS marker present in org layout?:"
grep -n "org-menu-minimal:start" resources/views/org/layout.blade.php || echo "(marker not found)"

echo -e "\n=== END CHECK ==="
