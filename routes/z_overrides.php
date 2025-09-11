<?php

use Illuminate\Support\Facades\Route;

Route::domain("admin.swaeduae.ae")->group(function () {
    Route::get("/login", function () { return view("auth.admin_login"); })->name("admin.login");
    Route::get("/admin/login", function () { return view("auth.admin_login"); });
});
