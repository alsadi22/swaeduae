#!/usr/bin/env bash
set -euo pipefail
echo "== routes =="; php artisan route:list | egrep -i "admin/login|admin/dashboard"
echo "== gate ==";  php artisan tinker --execute="use Illuminate\Support\Facades\Gate; echo Gate::has('isAdmin')?'OK':'MISSING';"
echo "== admin user =="; php artisan tinker --execute="use Illuminate\Support\Facades\DB; echo DB::table('users')->where('is_admin',1)->orWhere('role','admin')->count().PHP_EOL;"
echo "== views =="; ls -l resources/views/admin/dashboard.blade.php resources/views/admin/dashboard_compat_fallback.blade.php 2>/dev/null || true
