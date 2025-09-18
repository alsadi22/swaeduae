<?php
use Illuminate\Support\Facades\Route;
Route::middleware([web,guest])->group(function () {
    Route::get(/register, function () { return view(auth.register); })->name(register);
    Route::get(/org/register, function () { return view(org.auth.register); })->name(org.register);
});
