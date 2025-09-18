<?php
use Illuminate\Support\Facades\Route;
Route::middleware("web")->get("/", fn() => view("public.home"))->name("home.public");
