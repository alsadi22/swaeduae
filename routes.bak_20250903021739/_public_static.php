<?php
use Illuminate\Support\Facades\Route;
Route::get('/', function () { return view('home'); });                 // unnamed on purpose
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');
