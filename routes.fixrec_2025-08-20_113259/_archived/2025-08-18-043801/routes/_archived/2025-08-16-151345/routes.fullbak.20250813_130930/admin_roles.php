<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class, \App\Http\Middleware\AdminActionLogger::class])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/roles', [\App\Http\Controllers\Admin\RoleAdminController::class, 'index'])->name('roles.index');
        Route::post('/roles', [\App\Http\Controllers\Admin\RoleAdminController::class, 'store'])->name('roles.store');
        Route::post('/roles/{id}/perms', [\App\Http\Controllers\Admin\RoleAdminController::class, 'updatePermissions'])->name('roles.perms');
        Route::post('/perms', [\App\Http\Controllers\Admin\RoleAdminController::class, 'createPermission'])->name('perms.store');
    });
