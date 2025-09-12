<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateVerifyController;
use App\Http\Controllers\VolunteerHoursController;

// Public certificate verify
Route::get('/verify/{id}', [CertificateVerifyController::class, 'verify'])->name('cert.verify');

// Volunteer hours (auth required)
Route::middleware(['web','auth'])->get('/volunteer/hours', [VolunteerHoursController::class, 'index'])->name('volunteer.hours');
Route::middleware(['web','auth'])->post('/volunteer/hours', [VolunteerHoursController::class, 'store'])->name('volunteer.hours.store');
