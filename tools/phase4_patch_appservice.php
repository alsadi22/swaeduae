<?php
$f = 'app/Providers/AppServiceProvider.php';
$c = file_get_contents($f);

$addUse = function(string $use) use (&$c){
    if (strpos($c, $use) === false) {
        $c = preg_replace('/^(namespace[^\n]+\n)/', "$1$use\n", $c, 1);
    }
};
$addUse("use Illuminate\\Support\\Facades\\URL;");
$addUse("use Illuminate\\Support\\Facades\\RateLimiter;");
$addUse("use Illuminate\\Cache\\RateLimiting\\Limit;");

// Ensure boot() exists
if (strpos($c, 'function boot(') === false) {
    $c = preg_replace(
        '/class\s+AppServiceProvider\s+extends\s+ServiceProvider\s*\{/',
        "$0\n    public function boot(): void\n    {\n    }\n",
        $c, 1
    );
}

// Helper: inject line(s) inside the start of boot() if missing
$injectIntoBoot = function(string $needle, string $insert) use (&$c){
    if (strpos($c, $needle) !== false) return;
    $c = preg_replace(
        '/(public function boot\([^\)]*\)\s*:\s*void\s*\{\s*)/m',
        "$1$insert",
        $c,
        1
    );
};

// Enforce https in production
$injectIntoBoot("URL::forceScheme('https')",
    "        if (config('app.env') === 'production') { URL::forceScheme('https'); }\n");

// Global limiter 120 rpm / IP
$injectIntoBoot("RateLimiter::for('global'",
    "        RateLimiter::for('global', fn(\$request) => [Limit::perMinute(120)->by(\$request->ip())]);\n");

// Login limiter 5 rpm / email+IP
$injectIntoBoot("RateLimiter::for('login'",
    "        RateLimiter::for('login', function (\$request) {\n".
    "            \$id = (string) (\$request->input('email') ?? 'guest');\n".
    "            return [Limit::perMinute(5)->by(\$id.'|'.\$request->ip())];\n".
    "        });\n");

file_put_contents($f, $c);
echo "AppServiceProvider patched\n";
