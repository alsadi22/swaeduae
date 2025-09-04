<?php
$f = 'routes/web.php';
$c = file_get_contents($f);
if ($c === false) { fwrite(STDERR,"cannot read $f\n"); exit(1); }

$orig = $c;

/* 1) Remove any Admin DashboardController import (with/without alias) */
$c = preg_replace('/^\s*use\s+App\\\\Http\\\\Controllers\\\\Admin\\\\DashboardController(?:\s+as\s+AdminDashboard)?\s*;\s*$/m', '', $c);

/* 2) Remove ALL existing /admin route groups */
$c = preg_replace(
    "#Route::middleware\\(\\['web','auth'[^\\]]*\\]\\)\\s*(?:->withoutMiddleware\\([^)]*\\)\\s*)?->prefix\\('admin'\\)\\s*->name\\('admin\\.'\\)\\s*->group\\(\\s*function\\s*\\(\\)\\s*\\{[\\s\\S]*?\\}\\);\\s*#",
    "",
    $c
);

/* 3) Ensure there is ONE clean /admin group using the real controller (FQCN, no alias) */
$adminGroup = <<<PHPBLOCK

Route::middleware(['web','auth','role:admin'])
    ->withoutMiddleware([\App\Http\Middleware\EnforceOrgRegistration::class])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    });

PHPBLOCK;
if (strpos($c, "->prefix('admin')->name('admin.')") === false) {
    $c = rtrim($c)."\n".$adminGroup;
}

/* 4) Ensure POST /logout is named logout.perform */
if (preg_match("#->post\\('/logout'.*?->name\\('logout'\\)\\);#s", $c)) {
    $c = preg_replace(
        "#(->post\\('/logout'.*?->name\\(')logout('\\)\\);)#s",
        "$1logout.perform$2",
        $c,
        1
    );
} elseif (strpos($c, "logout.perform") === false) {
    $c .= "\nRoute::middleware(['web','auth'])->post('/logout', [\\App\\Http\\Controllers\\Auth\\SimpleLoginController::class,'logout'])->name('logout.perform');\n";
}

/* Write back if changed */
if ($c !== $orig) file_put_contents($f, $c);
echo "admin+logout fixed\n";
