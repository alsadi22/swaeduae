<?php

use Illuminate\Support\Facades\Route;

Route::domain(env(ADMIN_DOMAIN, admin.swaeduae.ae))->group(function () {
    // Admin login (Argon)
    Route::get(/login, fn() =>
        view(view()->exists(auth.admin_login) ? auth.admin_login : auth.login)
    )->name(admin.login);

    // In case anything links to /admin/login on the admin host
    Route::get(/admin/login, fn() =>
        view(view()->exists(auth.admin_login) ? auth.admin_login : auth.login)
    );
});
