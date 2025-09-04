<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\My\DashboardController;
use App\Http\Controllers\My\CertificatesController;
use App\Http\Controllers\My\HoursController;
use App\Http\Controllers\My\ApplicationsController;
use App\Http\Controllers\My\ProfileController;

Route::middleware(['web','auth'])->group(function () {

    // Dashboard
    Route::get('/my', [DashboardController::class, 'index'])->name('vol.dashboard');

    // Certificates
    Route::get('/my/certificates', [CertificatesController::class, 'index'])->name('my.certificates.index');
    Route::get('/my/certificates/{id}/download', [CertificatesController::class, 'download'])->name('my.certificates.download');

    // Hours
    Route::get('/my/hours', [HoursController::class, 'index'])->name('my.hours');

    // Applications
    Route::get('/my/applications', [ApplicationsController::class, 'index'])->name('my.applications');

    // Profile
    Route::get('/my/profile', [ProfileController::class, 'show'])->name('vol.profile');
    Route::post('/my/profile', [ProfileController::class, 'update'])->name('vol.profile.update');

    // Back-compat aliases if old blades link to /profile or /my/certs
    Route::get('/profile', fn() => redirect()->route('vol.profile'))->name('profile.legacy');
});
