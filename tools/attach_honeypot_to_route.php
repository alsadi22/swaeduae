<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

$uri = $argv[1] ?? 'org/register';
$method = 'POST';

$router = app('router');
try {
    $route = $router->getRoutes()->match(Request::create('/'.$uri, $method));
} catch (Throwable $e) {
    fwrite(STDERR, "[ERR] route not found: $method $uri — ".$e->getMessage()."\n");
    exit(1);
}

$action = $route->getActionName(); // e.g. App\Http\Controllers\Org\RegisterController@submit
if (stripos($action, 'Closure') !== false) {
    fwrite(STDERR, "[ERR] $method $uri is a Closure route; edit routes file to add honeypot.\n");
    exit(2);
}

[$class, $fn] = explode('@', $action, 2);
$ref = new ReflectionClass($class);
$file = $ref->getFileName();
if (!$file || !is_file($file)) { fwrite(STDERR,"[ERR] controller file not found for $class\n"); exit(3); }

$code = file_get_contents($file);
$orig = $code;

$needle = "\\App\\Http\\Middleware\\Honeypot::class";
$hasHp = (strpos($code, $needle) !== false) && (strpos($code, "only('".$fn."')") !== false);

// Insert into existing constructor or create one
if (!$hasHp) {
    if (preg_match('/function\s+__construct\s*\([^)]*\)\s*\{/', $code, $m, PREG_OFFSET_CAPTURE)) {
        $pos = $m[0][1] + strlen($m[0][0]);
        $insert = "\n        \$this->middleware(\\App\\Http\\Middleware\\Honeypot::class)->only('".$fn."');\n";
        $code = substr($code,0,$pos) . $insert . substr($code,$pos);
    } else {
        // Add constructor after class opening brace
        if (preg_match('/class\s+[A-Za-z0-9_\\\\]+\s*[^{]*\{/', $code, $m, PREG_OFFSET_CAPTURE)) {
            $pos = $m[0][1] + strlen($m[0][0]);
            $ctor = "\n    public function __construct()\n    {\n        \$this->middleware(\\App\\Http\\Middleware\\Honeypot::class)->only('".$fn."');\n    }\n";
            $code = substr($code,0,$pos) . $ctor . substr($code,$pos);
        } else {
            fwrite(STDERR,"[ERR] could not find class body in $file\n");
            exit(4);
        }
    }
}

if ($code !== $orig) {
    copy($file, $file.'.bak_'.date('Ymd_His'));
    file_put_contents($file, $code);
    echo "[OK] honeypot attached to $class@$fn via $file\n";
} else {
    echo "[SKIP] honeypot already present for $class@$fn\n";
}
