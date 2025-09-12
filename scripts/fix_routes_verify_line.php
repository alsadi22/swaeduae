<?php
$path = __DIR__ . '/../routes/web.php';
$c = file_get_contents($path);
if ($c === false) { fwrite(STDERR, "Cannot read routes/web.php\n"); exit(1); }

# Normalize any broken controller reference for the verify route
$c = preg_replace(
    "#Route::get\\('/verify/\\{code\\}',\\s*\\[(.*?)CertificateController::class,#",
    "Route::get('/verify/{code}', [\\\\App\\\\Http\\\\Controllers\\\\CertificateController::class,",
    $c
);

file_put_contents($path, $c);
echo "verify() route controller reference normalized.\n";
