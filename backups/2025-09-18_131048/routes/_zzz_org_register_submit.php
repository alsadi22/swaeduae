<?php
use Illuminate\Support\Facades\Route;
Route::middleware(["web","guest"])
    ->post("/org/register", fn() => response()->noContent())
    ->name("org.register.submit");
