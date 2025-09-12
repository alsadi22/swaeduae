<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

$uri = 'org/register';
$method = 'POST';

$router = app('router');

try {
    $route = $router->getRoutes()->match(Request::create('/'.$uri, $method));
} catch (Throwable $e) {
    fwrite(STDERR, "[ERR] route not found: $method $uri — ".$e->getMessage()."\n");
    exit(1);
}

$action = $route->getAction();
$uses   = $action['uses'] ?? null;

// We expect a Closure here
if (!($uses instanceof Closure)) {
    echo "[SKIP] $method $uri is not a Closure route (already controller-based)\n";
    exit(0);
}

// Find the defining file/line for the Closure
$rf = new ReflectionFunction($uses);
$file = $rf->getFileName();
$line = $rf->getStartLine();

if (!$file || !is_file($file)) {
    fwrite(STDERR, "[ERR] cannot resolve route file\n"); exit(2);
}
$code = file_get_contents($file);
if ($code === false) { fwrite(STDERR,"[ERR] cannot read $file\n"); exit(3); }

// Find the Route::post(...) statement that contains the closure
// Strategy: match all Route::post( ... ); statements with offsets, pick the one whose
// span encloses the closure start line.
$pattern = '/Route::post\s*\((?:(?>[^()]+)|(?R))*\)\s*[^;]*;/m';
if (!preg_match_all($pattern, $code, $m, PREG_OFFSET_CAPTURE)) {
    fwrite(STDERR,"[ERR] could not locate Route::post(...) statements in $file\n"); exit(4);
}

// Convert start line to byte offset (approx via lines)
$lines = explode("\n", $code);
$offsetAtLineStart = 0;
for ($i=0; $i < max(0,$line-1); $i++) $offsetAtLineStart += strlen($lines[$i]) + 1;

$chosenIdx = -1;
$chosenStart = $chosenEnd = null;
foreach ($m[0] as $i => $match) {
    [$stmt, $start] = $match;
    $end = $start + strlen($stmt);
    if ($offsetAtLineStart >= $start && $offsetAtLineStart <= $end) {
        $chosenIdx = $i; $chosenStart = $start; $chosenEnd = $end; break;
    }
}
if ($chosenIdx === -1) { fwrite(STDERR,"[ERR] could not map closure line to a Route::post statement\n"); exit(5); }

$stmt = substr($code, $chosenStart, $chosenEnd - $chosenStart);

// Already has honeypot?
if (stripos($stmt, 'Honeypot') !== false || stripos($stmt, "->middleware('honeypot'") !== false) {
    echo "[SKIP] honeypot already present on POST $uri\n"; exit(0);
}

// Decide insertion point: before ->name(…) if present, else before the semicolon
$insPos = stripos($stmt, '->name(');
$injection = "->middleware(\\App\\Http\\Middleware\\Honeypot::class)";
if ($insPos !== false) {
    $patched = substr($stmt,0,$insPos) . $injection . substr($stmt,$insPos);
} else {
    // insert before trailing semicolon
    $semi = strrpos($stmt, ';');
    if ($semi === false) { fwrite(STDERR,"[ERR] malformed route statement (no semicolon)\n"); exit(6); }
    $patched = substr($stmt,0,$semi) . " ".$injection . substr($stmt,$semi);
}

// Write back
$before = substr($code, 0, $chosenStart);
$after  = substr($code, $chosenEnd);
copy($file, $file.'.bak_'.date('Ymd_His'));
file_put_contents($file, $before.$patched.$after);
echo "[OK] honeypot injected into $file for POST $uri\n";
