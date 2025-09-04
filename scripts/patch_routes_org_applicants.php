<?php
$path = __DIR__ . '/../routes/web.php';
$routes = file_get_contents($path);
$needle = "org.applicants.decision";
if (strpos($routes, $needle) !== false) { echo "Applicants routes already present.\n"; exit; }

$block = <<<PHPBLOCK

// Org applicants management
Route::middleware(['auth', \\App\\Http\\Middleware\\EnsureOrg::class])->prefix('org')->name('org.')->group(function () {
    Route::get('/opportunities/{event}/applicants', [\\App\\Http\\Controllers\\Org\\ApplicantsController::class,'index'])->name('applicants.index');
    Route::post('/opportunities/{event}/applicants/{app}/decision', [\\App\\Http\\Controllers\\Org\\ApplicantsController::class,'decision'])->name('applicants.decision');
});
PHPBLOCK;

file_put_contents($path, "\n".$block."\n", FILE_APPEND);
echo "Applicants routes appended.\n";
