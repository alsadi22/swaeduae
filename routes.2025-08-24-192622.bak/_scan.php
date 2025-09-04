<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\QrAttendanceController;

Route::middleware(['web','auth'])->group(function () {
    Route::get ('/scan',              [QrAttendanceController::class, 'index'])->name('scan.index');
    Route::post('/scan/checkin',      [QrAttendanceController::class, 'checkin'])->middleware('throttle:global')->name('scan.checkin');
    Route::post('/scan/checkout',     [QrAttendanceController::class, 'checkout'])->middleware('throttle:global')->name('scan.checkout');

    // Safety: if someone GETs these URLs, send them back to the form
    Route::get ('/scan/checkin',  fn() => redirect()->route('scan.index'))->name('scan.checkin.get');
    Route::get ('/scan/checkout', fn() => redirect()->route('scan.index'))->name('scan.checkout.get');
});
