<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\Schema;
function cols($t){ return Schema::hasTable($t)? implode(', ', Schema::getColumnListing($t)) : '(missing)'; }
echo "opportunities: ".cols('opportunities').PHP_EOL;
echo "volunteer_hours: ".cols('volunteer_hours').PHP_EOL;
echo "attendances: ".cols('attendances').PHP_EOL;
