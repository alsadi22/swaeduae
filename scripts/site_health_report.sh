#!/bin/bash
set -e
cd "$(dirname "$0")/.."

echo "================ SawaedUAE — Site Health Report ================"

echo -e "\n[PHP & Laravel]"
php -v | head -n 1
php artisan --version

echo -e "\n[Project Structure]"
printf "Top-level (key only):\n"
ls -ld app bootstrap config database public resources routes artisan || true
printf "\nRoutes dir:\n"; ls -1 routes | sed 's/^/ - /'
printf "\nControllers (top-level):\n"; find app/Http/Controllers -maxdepth 2 -type f | sed 's/^/ - /' | head -n 80

echo -e "\n[web.php Sprint includes]"
grep -n "SPRINT" routes/web.php || echo " - (no SPRINT include lines found)"

echo -e "\n[Key Routes Present]"
php artisan route:list | sed 's/[[:space:]]\+/ /g' | grep -E \
"(about|faq|partners$|^ *GET \s*regions|verify(/{code})?|opportunities($|/)|orgs\.public|partners\.apply|calendar(\.ics)?$|\.ics$|sitemap\.xml|robots\.txt|admin\.overview|admin\.export)" || true

echo -e "\n[Migrations Status]"
php artisan migrate:status || true

echo -e "\n[Storage Link & Certificates]"
if [ -L public/storage ]; then echo "public/storage symlink: OK"; else echo "public/storage symlink: MISSING"; fi
ls -l public/storage/certificates 2>/dev/null | tail -n 5 || echo "(no certificates folder/files yet)"

echo -e "\n[Logs — last 40 lines]"
tail -n 40 storage/logs/laravel.log 2>/dev/null || echo "(no laravel.log yet)"

echo -e "\n[Core Package Checks]"
php -r 'include "vendor/autoload.php"; echo (class_exists("Barryvdh\\DomPDF\\Facade\\Pdf")?"DOMPDF: OK":"DOMPDF: MISSING"), " | ", (class_exists("SimpleSoftwareIO\\QrCode\\Facades\\QrCode")?"QR Facade: OK":"QR Facade: MISSING"), PHP_EOL;'
php -m | grep -i imagick >/dev/null && echo "PHP imagick: OK" || echo "PHP imagick: MISSING (QR png fallback to svg recommended)"

echo -e "\n[DB Counts (read-only)]"
php scripts/db_counts.php 2>/dev/null || echo "(db_counts.php not found; creating a temporary one...)"

# create temporary db_counts if missing
if [ ! -f scripts/db_counts.php ]; then
  cat > scripts/db_counts.php <<'PHP'
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\Schema; use Illuminate\Support\Facades\DB;
$tables = ['users','opportunities','applications','volunteer_hours','certificates','partner_intake_submissions','audit_logs'];
foreach ($tables as $t) {
  if (Schema::hasTable($t)) { $c = DB::table($t)->count(); echo str_pad($t, thirty:=28, ' '),": $c\n"; }
  else { echo str_pad($t,28,' '),": (MISSING)\n"; }
}
PHP
  php scripts/db_counts.php
fi

echo -e "\n[Done]"
echo "================================================================"
