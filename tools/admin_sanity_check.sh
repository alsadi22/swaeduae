#!/usr/bin/env bash
set -euo pipefail
section(){ printf "\n\n===== %s =====\n" "$*"; }

section "Environment"
php -v | head -n1
php artisan --version

section "Routes — admin aliases & pages"
php artisan route:list | egrep -i "admin\.home|admin\.login|admin\.dashboard|admin\.users|admin\.hours|^ *GET *\| */admin($|/)"

section "Routes — login exists"
php artisan route:list | egrep -i "^ *GET *\| */login|^ *POST *\| */login" || echo "No /login route?"

section "Route files include _admin_bootstrap.php?"
grep -n "_admin_bootstrap.php" routes/web.php || echo "NOT referenced in routes/web.php"

section "Any routes using can:isAdmin middleware?"
grep -Rin "can:isAdmin" routes || echo "No explicit 'can:isAdmin' found in routes/*.php"

section "Files — Admin controllers & views"
ls -l app/Http/Controllers/Admin 2>/dev/null || echo "Admin controllers dir missing"
ls -l app/Http/Controllers/Admin/AdminController.php 2>/dev/null || echo "AdminController.php missing"
ls -l resources/views/admin 2>/dev/null || echo "admin views dir missing"
ls -l resources/views/admin/dashboard.blade.php 2>/dev/null || echo "dashboard view missing"

section "DB columns (users)"
php artisan tinker --execute="use Illuminate\\Support\\Facades\\Schema; echo 'is_admin='.(Schema::hasColumn('users','is_admin')?'YES':'NO').', role='.(Schema::hasColumn('users','role')?'YES':'NO').PHP_EOL;"

section "Gate registered? (isAdmin)"
php artisan tinker --execute="use Illuminate\\Support\\Facades\\Gate; echo Gate::has('isAdmin') ? 'YES'.PHP_EOL : 'NO'.PHP_EOL;"

section "Any admin users present?"
php artisan tinker --execute="use Illuminate\\Support\\Facades\\DB; echo 'is_admin=1 count: '.DB::table('users')->where('is_admin',1)->count().PHP_EOL;"

section "Layout presence (for admin views)"
[ -f resources/views/layouts/app.blade.php ] && echo "layouts/app.blade.php OK" || echo "layouts/app.blade.php MISSING"

echo -e '\nDone.'
