<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$email = $argv[1] ?? null;
$flag  = isset($argv[2]) ? (int)$argv[2] : 1;

if (!$email) {
    fwrite(STDERR, "Usage: php scripts/make_admin.php user@example.com [1|0]\n");
    exit(1);
}

$affected = DB::table('users')->where('email', $email)->update(['is_admin' => $flag]);
if ($affected > 0) {
    echo "Updated {$email}: is_admin = {$flag}\n";
} else {
    echo "No user found with email {$email}\n";
}
