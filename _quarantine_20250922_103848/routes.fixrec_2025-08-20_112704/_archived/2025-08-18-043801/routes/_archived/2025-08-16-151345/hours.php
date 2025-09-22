<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VolunteerHourController;

Route::middleware('auth')->group(function () {
    Route::get('/hours', [VolunteerHourController::class, 'index'])->name('hours.index');
    Route::post('/hours', [VolunteerHourController::class, 'store'])->name('hours.store');
});

Route::middleware(['auth','role:admin'])->group(function () {
    Route::put('/admin/hours/{hour}/status', [VolunteerHourController::class, 'setStatus'])->name('admin.hours.status');
});
