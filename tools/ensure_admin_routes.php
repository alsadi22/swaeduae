<?php
$F = __DIR__ . '/../routes/web.php';
$src = file_get_contents($F);
if ($src === false) { fwrite(STDERR,"Cannot read routes/web.php\n"); exit(1); }

$use = "use App\\Http\\Controllers\\Admin\\Auth\\AdminLoginController;";
if (strpos($src, $use) === false) {
    // add after opening tag if present, else prepend
    if (strpos($src, "<?php") === 0) {
        $src = "<?php\n$use\n" . substr($src, 6);
    } else {
        $src = $use . "\n" . $src;
    }
}

// Remove all existing POST /admin/login definitions to avoid duplicates
$src = preg_replace(
    '/Route::post\s*\(\s*[\'"]\/admin\/login[\'"][\s\S]*?;(\s*\/\/.*)?/i',
    '// removed old POST /admin/login route' . "\n",
    $src
);
// Remove all existing GET /admin/login definitions to avoid duplicates
$src = preg_replace(
    '/Route::get\s*\(\s*[\'"]\/admin\/login[\'"][\s\S]*?;(\s*\/\/.*)?/i',
    '// removed old GET /admin/login route' . "\n",
    $src
);

// Append our known-good block at the end
$stub = <<<'PHPSTUB'

// --- BEGIN: admin auth (clean) ---
Route::middleware(['web'])->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'show'])
        ->name('admin.login')
        ->middleware(['guest']);

    Route::post('/admin/login', [AdminLoginController::class, 'login'])
        ->name('admin.login.post')
        ->middleware(['guest'])
        ->withoutMiddleware([
            \App\Http\Middleware\Honeypot::class,      // custom class if exists
            'honeypot',                                 // alias
            \Spatie\Honeypot\ProtectAgainstSpam::class  // spatie class
        ]);
        // For testing only, if 419 persists after Kernel fix, temporarily add:
        // ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
});
// --- END: admin auth (clean) ---

PHPSTUB;

$src = rtrim($src) . "\n\n" . $stub . "\n";
file_put_contents($F, $src);
echo "Admin routes refreshed.\n";
