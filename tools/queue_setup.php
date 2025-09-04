<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

if (!Schema::hasTable('failed_jobs')) {
    echo "[INFO] creating failed_jobs...\n";
    passthru('/usr/local/bin/php artisan queue:failed-table', $a);
    passthru('/usr/local/bin/php artisan migrate --force', $b);
} else {
    echo "[SKIP] failed_jobs exists\n";
}
