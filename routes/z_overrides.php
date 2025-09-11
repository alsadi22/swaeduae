<?php

use Illuminate\Support\Facades\Route;

Route::domain("admin.swaeduae.ae")->group(function () {
    Route::get("/login", function () {
        return view(view()->exists("auth.admin_login") ? "auth.admin_login" : "auth.login");
    })->name("admin.login");

    Route::get("/admin/login", function () {
        return view(view()->exists("auth.admin_login") ? "auth.admin_login" : "auth.login");
    });
});
