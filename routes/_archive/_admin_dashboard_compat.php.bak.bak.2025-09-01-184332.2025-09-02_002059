<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardCompatController;

Route::prefix('admin')->middleware(['web','auth','can:isAdmin'])->group(function () {
    Route::get('/dashboard', [DashboardCompatController::class, 'index'])->name('admin.dashboard');
    Route::get('/', fn() => redirect()->route('admin.dashboard'))->name('admin.home');
});
