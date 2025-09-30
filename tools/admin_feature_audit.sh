#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
STAMP="$(date '+%F_%H%M%S')"
OUT="public/health/admin-audit-$STAMP.txt"
exec > >(tee "$OUT") 2>&1

echo "=== ADMIN FEATURE AUDIT $STAMP ==="

echo "== Route names (volunteer/org/admin) =="
php artisan tinker --execute='
use Illuminate\Support\Facades\Route;
$names=[
 "login","register","my.profile",       // volunteers
 "org.login","org.register","org.dashboard",   // organization
 "admin.login","admin.dashboard","admin.users.index","admin.opportunities.index", // admin
 // suggested new admin sections:
 "admin.applicants.index","admin.attendance.index","admin.hours.all",
 "admin.certificates.index","admin.reports.index","admin.settings.index","admin.logout"
];
foreach($names as $n){ echo $n.": ".(Route::has($n)?"YES":"NO").PHP_EOL; }'

echo; echo "== Admin routes + middleware =="
php artisan tinker --execute='
use Illuminate\Support\Facades\Route;
foreach(Route::getRoutes() as $r){
 $n=$r->getName();
 if($n && str_starts_with($n,"admin.")){
   echo $n." | ".$r->uri()." | ".implode(",", $r->gatherMiddleware()).PHP_EOL;
 }}'

echo; echo "== Argon theme presence (views/assets) =="
ls -ld resources/views/admin resources/views/admin/* 2>/dev/null || true
ls -ld resources/views/admin/layouts* 2>/dev/null || true
ls -ld resources/views/admin/auth 2>/dev/null || true
grep -RIn --color=never -m1 -E "Argon|argon" resources/views admin 2>/dev/null || true
ls -ld public/assets* public/argon* 2>/dev/null || true

echo; echo "== Admin controllers (exist/missing) =="
for c in \
  app/Http/Controllers/Admin/DashboardController.php \
  app/Http/Controllers/Admin/UserController.php \
  app/Http/Controllers/Admin/OpportunityController.php \
  app/Http/Controllers/Admin/ApplicantsController.php \
  app/Http/Controllers/Admin/AttendanceAdminController.php \
  app/Http/Controllers/Admin/HoursReportController.php \
  app/Http/Controllers/Admin/CertificateController.php \
  app/Http/Controllers/Admin/ReportsController.php \
  app/Http/Controllers/Admin/SettingsController.php
do [ -f "$c" ] && echo "[OK]  $c" || echo "[MISS] $c"; done

echo; echo "== Key admin views (exist/missing) =="
for v in \
  resources/views/admin/dashboard.blade.php \
  resources/views/admin/users/index.blade.php \
  resources/views/admin/opportunities/index.blade.php \
  resources/views/admin/applicants/index.blade.php \
  resources/views/admin/attendance/index.blade.php \
  resources/views/admin/hours/index.blade.php \
  resources/views/admin/certificates/index.blade.php \
  resources/views/admin/reports/index.blade.php \
  resources/views/admin/settings/index.blade.php \
  resources/views/admin/auth/login.blade.php \
  resources/views/admin/layout.blade.php \
  resources/views/admin/layouts/guest.blade.php
do [ -f "$v" ] && echo "[OK]  $v" || echo "[MISS] $v"; done

echo; echo "== DB schema (tables) =="
php artisan tinker --execute='
use Illuminate\Support\Facades\Schema;
foreach(["users","organizations","opportunities","applicants","attendances","hours","certificates"] as $t){
 echo $t.": ".(Schema::hasTable($t)?"YES":"NO").PHP_EOL;
}'

echo; echo "== HTTP probes (status + Location) =="
for p in /login /register /profile /org/login /org/register /org/dashboard /admin /admin/login /admin/dashboard; do
  printf "%-18s -> " "$p"
  curl -sI -k https://swaeduae.ae$p | awk '/^HTTP|^Location/ {print}'
done

echo; echo "Report saved: $OUT"
