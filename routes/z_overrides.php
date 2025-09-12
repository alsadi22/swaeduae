<?php
use Illuminate\Support\Facades\Route;

Route::domain('admin.swaeduae.ae')->middleware(['web'])->get('/', function () { return redirect()->to('/admin'); });

Route::domain('admin.swaeduae.ae')->middleware(['web','guest'])->get('/admin/login', function () { return redirect()->to('/login'); })->name('admin.login');
