<?php
$path = __DIR__ . '/../routes/web.php';
$c = file_get_contents($path);
if (strpos($c, "prefix('org')->name('org.')") !== false) { echo "Org routes already present.\n"; exit; }
$block = <<<PHPBLOCK

Route::middleware(['auth','org'])->prefix('org')->name('org.')->group(function () {
    Route::get('/setup', [\\App\\Http\\Controllers\\Org\\SetupController::class,'form'])->name('setup.form');
    Route::post('/setup', [\\App\\Http\\Controllers\\Org\\SetupController::class,'store'])->name('setup.store');

    Route::get('/dashboard', [\\App\\Http\\Controllers\\Org\\DashboardController::class,'index'])->name('dashboard');

    Route::get('/opportunities', [\\App\\Http\\Controllers\\Org\\OpportunityController::class,'index'])->name('opportunities.index');
    Route::get('/opportunities/create', [\\App\\Http\\Controllers\\Org\\OpportunityController::class,'create'])->name('opportunities.create');
    Route::post('/opportunities', [\\App\\Http\\Controllers\\Org\\OpportunityController::class,'store'])->name('opportunities.store');
});
PHPBLOCK;

file_put_contents($path, "\n".$block."\n", FILE_APPEND);
echo "Org routes appended.\n";
