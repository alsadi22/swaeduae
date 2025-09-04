<?php

use Illuminate\Support\Facades\Route;

Route::any('/login/organization', fn() => redirect()->route('org.login'))->name('login.organization.redirect');
