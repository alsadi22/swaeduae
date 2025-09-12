<?php
Route::domain('admin.swaeduae.ae')->middleware('web')->get('/', fn () => redirect('/admin'));
Route::domain('admin.swaeduae.ae')->middleware(['web','guest'])->get('/admin/login', fn () => redirect('/login'))->name('admin.login');
