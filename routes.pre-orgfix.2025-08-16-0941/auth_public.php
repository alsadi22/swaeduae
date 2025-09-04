<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

# Logout (supports GET and POST so the existing <a href="{{ route('logout') }}"> works)
Route::match(['GET','POST'], '/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/')->with('status', __('Logged out'));
})->name('logout.public');

# Partners
Route::get('/partners', function () {
    return view()->exists('pages.partners')
        ? view('pages.partners')
        : view('welcome')->with('message', __('Partners page coming soon.'));
})->name('public.partners');

# Categories
Route::get('/categories', function () {
    if (class_exists(\App\Http\Controllers\CategoryController::class)) {
        return app(\App\Http\Controllers\CategoryController::class)->index();
    }
    return view()->exists('pages.categories')
        ? view('pages.categories')
        : view('welcome')->with('message', __('Categories page coming soon.'));
})->name('public.categories');

/* Public login redirects to org login */
Route::middleware(['web','guest'])->get('/login', function () {
    return redirect('/org/login');
})->name('login');
