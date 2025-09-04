<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| These routes handle user authentication for volunteers and organizations.
| If you decide to implement different registration forms for each role,
| define separate controllers and routes here.
*/
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| Use resource controllers for events, opportunities, news and certificates.
| This makes the routes table cleaner and opens the door to adding CRUD
| operations (create, edit, update, destroy) later.
*/
Route::resource('events', EventController::class)->only(['index', 'show']);
Route::resource('opportunities', OpportunityController::class)->only(['index', 'show']);
Route::resource('news', NewsController::class)->only(['index', 'show']);
Route::resource('certificates', CertificateController::class)->only(['index', 'show']);

// Profile - available to authenticated users
Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth')->name('profile.show');

// Home page (fallback)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Add more routes here as needed
