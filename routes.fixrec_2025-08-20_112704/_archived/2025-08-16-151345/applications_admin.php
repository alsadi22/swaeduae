<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ApplicationAdminController;

Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/applications', [ApplicationAdminController::class, 'index'])->name('applications.index');
    Route::put('/applications/{application}', [ApplicationAdminController::class, 'updateStatus'])->name('applications.update');
});
