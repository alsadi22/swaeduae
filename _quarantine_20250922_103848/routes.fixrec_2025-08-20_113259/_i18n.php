<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
Route::get('/lang/{locale}', function (string $locale) {
    $allowed = ['ar','en'];
    if (!in_array($locale, $allowed, true)) { $locale = 'ar'; }
    session(['locale' => $locale]);
    app()->setLocale($locale);
    return back();
})->name('locale.switch');
