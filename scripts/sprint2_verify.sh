#!/bin/bash
set -e
cd /home3/vminingc/swaeduae.ae/laravel-app
echo "=== Sprint 2 Verify ==="

# Routes present?
php artisan route:list | grep -E "verify($|/\\{code\\})|admin/certificates|generate-certs" || echo "[WARN] Some Sprint 2 routes missing."

# PHP-based DB checks (no mysql CLI needed)
php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$kernel = $app->make(Illuminate\\Contracts\\Console\\Kernel::class);
$kernel->bootstrap();
use Illuminate\\Support\\Facades\\Schema; use Illuminate\\Support\\Facades\\DB;
$checks=[
 "certificates (table)"=>Schema::hasTable("certificates"),
 "certificate_deliveries (table)"=>Schema::hasTable("certificate_deliveries"),
 "certificates.code"=>Schema::hasColumn("certificates","code"),
 "certificates.file_path"=>Schema::hasColumn("certificates","file_path"),
];
foreach($checks as $k=>$ok){ echo str_pad($k,36," ").": ".($ok?"OK":"MISSING").PHP_EOL; }
$cntC=Schema::hasTable("certificates")? DB::table("certificates")->count():0;
$cntD=Schema::hasTable("certificate_deliveries")? DB::table("certificate_deliveries")->count():0;
echo "certificates rows: $cntC".PHP_EOL;
echo "deliveries rows:   $cntD".PHP_EOL;
'

# Storage link exists?
[ -L public/storage ] && echo "storage:link OK" || echo "[WARN] storage link missing (run: php artisan storage:link)"

echo "=== Verify done ==="
