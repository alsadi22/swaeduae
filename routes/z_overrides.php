<?php
use Illuminate\Support\Facades\Route;

Route::domain(admin.swaeduae.ae)->group(function () {
    // Primary admin login page (Argon view)
    Route::get(/login, function () {
        return view(view()->exists(auth.admin_login) ? auth.admin_login : auth.login);
    })->name(admin.login);

    // Also serve /admin/login on the admin host, in case templates link there
    Route::get(/admin/login, function () {
        return view(view()->exists(auth.admin_login) ? auth.admin_login : auth.login);
    });
});
