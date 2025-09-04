<?php
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== Effective routes (admin/org login+dashboard) ===\n";
foreach (Route::getRoutes() as $r) {
    $u = $r->uri();
    if (in_array($u, ['admin/login','admin/dashboard','org/login','org/dashboard'])) {
        printf("%-10s /%-18s name=%-22s action=%s\n    mw=[%s]\n",
            implode('|',$r->methods()), $u, ($r->getName()??''), ($r->getActionName()??'closure'),
            implode(',', $r->gatherMiddleware())
        );
    }
}
