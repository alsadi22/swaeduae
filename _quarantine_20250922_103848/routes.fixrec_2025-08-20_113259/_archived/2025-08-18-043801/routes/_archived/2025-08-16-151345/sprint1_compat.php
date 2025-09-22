<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ApplicationReviewController;

Route::middleware(['web','auth','admin'])->group(function () {
    // Backward-compatibility alias for old menu link
    Route::get('/admin/apps', [ApplicationReviewController::class, 'index'])->name('admin.apps.index');
});
