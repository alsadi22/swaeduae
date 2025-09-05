<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;

// Boot Laravel
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// Add a sample event
\App\Models\Event::create([
    'title' => 'Sample Event',
    'description' => 'This is a test event.',
    'city' => 'Dubai',
    'date' => now()->addDays(3),
    'status' => 'upcoming'
]);

echo "Sample event added!\n";
