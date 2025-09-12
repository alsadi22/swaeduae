<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminLoginController;

Route::middleware(['web'])->group(function () {
    // GET /admin -> if already admin, go to dashboard; otherwise show admin login (separate from public login)
    Route::get('/admin', function (Request $r) {
        $u = Auth::user();
        if ($u && Gate::forUser($u)->allows('isAdmin')) {
            return redirect()->route('admin.dashboard');
        }
        return app(AdminLoginController::class)->show($r);
    })->name('admin.welcome');

    // Dedicated admin login endpoints
    Route::get('/admin/login', [AdminLoginController::class, 'show'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.post');

    // Admin logout
    Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});
