<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::middleware("web")->get("/contact", [ContactController::class, "show"])->name("contact.get");
Route::middleware("web")->post("/contact", [ContactController::class, "send"])->name("contact.submit");
