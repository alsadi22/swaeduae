<?php
$f='app/Providers/AppServiceProvider.php';
$c=file_get_contents($f);

// Remove wrong imports like: use App\Providers\RateLimiter; or ...\Limit;
$c=preg_replace('/^use\s+App\\\\Providers\\\\(RateLimiter|Limit);\s*$/m','',$c);

// Ensure correct imports exist once (right after the namespace line)
$ns = 'namespace App\\Providers;';
if (strpos($c,$ns)===false) { fwrite(STDERR,"Unexpected namespace in $f\n"); exit(1); }
$uses = [
    "use Illuminate\\Support\\Facades\\URL;",
    "use Illuminate\\Support\\Facades\\RateLimiter;",
    "use Illuminate\\Cache\\RateLimiting\\Limit;",
];
foreach ($uses as $u) {
    if (strpos($c,$u)===false) {
        $c=preg_replace('/^(namespace\s+App\\\\Providers;[^\n]*\n)/m', "$1$u\n", $c, 1);
    }
}

// Ensure boot() exists
if (!preg_match('/function\s+boot\s*\(/',$c)) {
    $c=preg_replace(
        '/class\s+AppServiceProvider\s+extends\s+ServiceProvider\s*\{/',
        "$0\n    public function boot(): void\n    {\n    }\n",
        $c,1
    );
}

// Helper to inject a line inside boot() only once
$inject=function($needle,$insert) use (&$c){
    if (strpos($c,$needle)!==false) return;
    $c=preg_replace('/(public function boot\s*\([^)]*\)\s*(?::\s*\w+)?\s*\{\s*)/m', "$1$insert", $c, 1);
};

// Enforce https in prod
$inject("URL::forceScheme('https')","        if (config('app.env') === 'production') { URL::forceScheme('https'); }\n");

// Global rate limiter
$inject("RateLimiter::for('global'","        RateLimiter::for('global', fn(\$request) => [Limit::perMinute(120)->by(\$request->ip())]);\n");

// Login rate limiter
$inject("RateLimiter::for('login'","        RateLimiter::for('login', function (\$request) {\n            \$id = (string) (\$request->input('email') ?? 'guest');\n            return [Limit::perMinute(5)->by(\$id.'|'.\$request->ip())];\n        });\n");

file_put_contents($f,$c);
echo "patched\n";
