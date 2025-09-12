<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Schema;
$have = (Schema::hasTable('acl_roles') && Schema::hasTable('acl_permissions') && Schema::hasTable('acl_model_has_roles'));
echo $have ? "have\n" : "missing\n";
