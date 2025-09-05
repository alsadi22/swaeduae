<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;

Route::get('/', function () { return view('welcome'); })->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');
Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
Route::get('/region/sharjah', [EventController::class, 'sharjah'])->name('region.sharjah');
Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');

Route::post('/lang/switch', function (Request $request) {
    $lang = $request->input('lang');
    if (in_array($lang, ['en', 'ar'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }
    return back();
})->name('lang.switch');

Route::middleware('auth')->group(function () {
    Route::get('/volunteer/profile', [VolunteerController::class, 'profile'])->name('volunteer.profile');
    Route::post('/volunteer/events/{eventId}/register', [VolunteerController::class, 'registerEvent'])->name('volunteer.registerEvent');
    Route::post('/volunteer/kyc/upload', [VolunteerController::class, 'uploadKyc'])->name('volunteer.uploadKyc');
    Route::get('/volunteer/resume', [VolunteerController::class, 'resume'])->name('volunteer.resume');
    Route::get('/volunteer/certificate/{certId}', [VolunteerController::class, 'generateCertificate'])->name('volunteer.generateCertificate');

    Route::get('/payment', [PaymentController::class, 'paymentPage'])->name('payments.page');
    Route::post('/payment/stripe', [PaymentController::class, 'processStripe'])->name('payments.processStripe');
    Route::post('/payment/paytabs', [PaymentController::class, 'processPayTabs'])->name('payments.processPayTabs');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payments.success');
});

Route::prefix('admin')->middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'listUsers'])->name('admin.users');
    Route::post('/users/toggle/{id}', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle');
    Route::get('/events', [AdminController::class, 'listEvents'])->name('admin.events');
    Route::get('/certificates', [AdminController::class, 'listCertificates'])->name('admin.certificates');
    Route::post('/backup', [AdminController::class, 'backup'])->name('admin.backup');
});

Auth::routes();
