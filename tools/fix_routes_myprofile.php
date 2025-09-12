<?php
$path = 'routes/web.php';
$c0 = file_get_contents($path);
$c  = $c0;

// 1) Drop legacy conditional my.profile block: if (!Route::has('my.profile')) { ... }
$c = preg_replace("#if\\s*\\(\\s*!Route::has\\(\\s*['\"]my\\.profile['\"]\\s*\\)\\s*\\)\\s*\\{.*?\\}#s", "", $c);

// 2) Drop any single-line routes that register /my/profile (defensive)
$c = preg_replace("#Route::[^;]*['\"]/my/profile['\"][^;]*;\\s*#s", "", $c);

// 3) Normalize invalid Route::prefix([ 'x' ]) → Route::prefix('x')
$c = preg_replace(
    "#Route::prefix\\(\\s*\\[\\s*(['\"])([^'\"\\],]+)\\1\\s*(?:,\\s*[^\\]]*)?\\]\\s*\\)#",
    "Route::prefix('\$2')",
    $c
);

// 4) Append canonical protected routes once
if (strpos($c, "name('my.profile');") === false) {
    $c .= PHP_EOL."/* === Volunteer Profile (protected) — canonical === */
Route::middleware(['web','auth'])->get(
  '/my/profile',
  [\\App\\Http\\Controllers\\My\\ProfileController::class, 'show']
)->name('my.profile');

Route::middleware(['web','auth'])->post(
  '/my/profile',
  [\\App\\Http\\Controllers\\My\\ProfileController::class, 'update']
)->name('my.profile.update');".PHP_EOL;
}

if ($c !== $c0) {
    file_put_contents($path, $c);
    echo "routes fixed\n";
} else {
    echo "routes unchanged\n";
}
