<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ExportController;

Route::middleware(['web','auth','admin'])->group(function () {
    Route::get('/admin/overview', [AdminDashboardController::class, 'index'])->name('admin.overview');

    // CSV exports
    Route::get('/admin/export/users.csv',         [ExportController::class, 'users'])->name('admin.export.users');
    Route::get('/admin/export/hours.csv',         [ExportController::class, 'hours'])->name('admin.export.hours');
    Route::get('/admin/export/certificates.csv',  [ExportController::class, 'certificates'])->name('admin.export.certificates');
    Route::get('/admin/export/applications.csv',  [ExportController::class, 'applications'])->name('admin.export.applications');
});
