<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\AdminController;

Route::prefix('admin')->middleware(['web','auth','can:isAdmin'])->group(function () {
    // Primary: /admin/users -> admin.users.index
    Route::get('/users', [UserAdminController::class, 'index'])->name('admin.users.index');

    // Alias: /admin/users/index -> admin.users  (back-compat for old blades)
    Route::get('/users/index', [UserAdminController::class, 'index'])->name('admin.users');

    // Keep existing toggle action
    Route::post('/users/toggle/{id}', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle');
});
