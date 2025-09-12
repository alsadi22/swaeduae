<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();

try {
  \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>PDF OK</h1>')->setPaper('a4');
  echo "DOMPDF: OK\n";
} catch (\Throwable $e) { echo "DOMPDF ERROR: ".$e->getMessage()."\n"; }

try {
  $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(120)->generate('test');
  echo "QR SVG: OK (".strlen($svg)." chars)\n";
} catch (\Throwable $e) { echo "QR ERROR: ".$e->getMessage()."\n"; }
