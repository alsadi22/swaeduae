#!/usr/bin/env bash
set -euo pipefail
section(){ printf "\n\n===== %s =====\n" "$*"; }

section "Route bound to admin.dashboard"
php artisan route:list | egrep -i "admin\.dashboard|^ *GET *\| */admin($|/)"

section "Controller file + 'dashboard' method signature"
ls -l app/Http/Controllers/Admin/AdminController.php || true
grep -n "function *dashboard" -n app/Http/Controllers/Admin/AdminController.php || echo "No dashboard() in AdminController?"

section "Blade view presence"
ls -l resources/views/admin/dashboard.blade.php || echo "dashboard.blade.php missing"

section "Gate sanity"
php artisan tinker --execute='use Illuminate\Support\Facades\Gate; echo "has isAdmin: ".(Gate::has("isAdmin")?"YES":"NO").PHP_EOL;'

section "Admin user sanity"
php artisan tinker --execute='use Illuminate\Support\Facades\DB; echo "admins: ".DB::table("users")->where("is_admin",1)->orWhere("role","admin")->count().PHP_EOL;'

section "Last error stack from laravel.log (if any)"
LOG=storage/logs/laravel.log
[ -f "$LOG" ] && awk 'BEGIN{p=0} /^\[[0-9-]+ [0-9:]+\] production\.ERROR/{p=1; buf=$0; next} { if(p){buf=buf"\n"$0} } END{print buf}' "$LOG" | tail -n 120 || echo "No log yet"

echo -e "\nDone."
