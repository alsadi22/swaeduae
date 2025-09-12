<?php
require __DIR__."/../vendor/autoload.php";
$app = require __DIR__."/../bootstrap/app.php";
$http = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

config()->set('app.env','local');    // only for this CLI invocation
config()->set('app.debug', true);    // only for this CLI invocation

function probe($http, $uri) {
    echo "=== $uri ===\n";
    $req = Illuminate\Http\Request::create($uri,'GET',[],[],[],['HTTPS'=>'on']);
    $res = $http->handle($req);
    $code = $res->getStatusCode();
    echo "STATUS: $code\n";
    if ($code >= 400) {
        $plain = strip_tags((string)$res->getContent());
        echo substr($plain, 0, 1200), "\n";
    }
    echo "\n";
}
probe($http,'/');
probe($http,'/about');
