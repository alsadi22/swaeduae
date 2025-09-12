<?php
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ----- Auth (login/register/logout) -----
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']->middleware(['honeypot','throttle:login']));
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']->middleware(['honeypot']))->name('register.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout.auth');

// ----- Password reset -----
Route::get('/password/forgot', [AuthController::class, 'showForgot'])->name('password.request');
Route::post('/password/forgot', [AuthController::class, 'sendForgot'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showReset'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');

// ----- Email verification -----
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->intended(route('home'));
})->middleware(['auth','signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return back();
    }
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', __('Verification link sent.'));
})->middleware(['auth','throttle:6,1'])->name('verification.send');
