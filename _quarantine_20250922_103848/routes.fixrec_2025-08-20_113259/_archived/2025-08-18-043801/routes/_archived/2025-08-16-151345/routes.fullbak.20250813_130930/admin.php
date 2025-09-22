<?php
use App\Http\Controllers\Admin\AttendanceController;


use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OpportunityController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\SettingController;

Route::prefix('admin')->as('admin.')->middleware(['web','auth', AdminMiddleware::class])->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('home');
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');

    Route::resource('users', UserController::class)->only(['index']);

    Route::resource('organizations', OrganizationController::class)->except(['show']);
    Route::resource('opportunities', OpportunityController::class)->except(['show']);
    Route::resource('events', EventController::class)->except(['show']);

    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/create', [CertificateController::class, 'create'])->name('certificates.create');
    Route::post('certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::get('certificates/{id}/pdf', [CertificateController::class, 'pdf'])->name('certificates.show.pdf');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('partners', PartnerController::class)->except(['show']);

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::get('settings/edit', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
});

// --- SawaedUAE additions: Admin learning requests review (protected) ---
Route::middleware(['web','auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')->as('admin.')->group(function () {
        Route::get('learning-requests', [\App\Http\Controllers\Admin\LearningAdminController::class, 'index'])->name('learning.index');
        Route::post('learning-requests/{id}', [\App\Http\Controllers\Admin\LearningAdminController::class, 'update'])->name('learning.update');
    });

// --- SawaedUAE: Admin Applications Review ---
Route::middleware(['web','auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')->as('admin.')->group(function () {
        Route::get('applications', [\App\Http\Controllers\Admin\ApplicationAdminController::class,'index'])->name('applications.index');
        Route::post('applications/{id}', [\App\Http\Controllers\Admin\ApplicationAdminController::class,'update'])->name('applications.update');
    });

// --- SawaedUAE: Admin attendance
Route::middleware(['web','auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')->as('admin.')->group(function () {
        Route::get('opportunities/{id}/qr', [\App\Http\Controllers\Admin\AttendanceAdminController::class, 'qr'])->name('attendance.qr');
        Route::get('attendance', [\App\Http\Controllers\Admin\AttendanceAdminController::class, 'index'])->name('attendance.index');
        Route::post('opportunities/{id}/attendance/manual', [\App\Http\Controllers\Admin\AttendanceAdminController::class, 'manual'])->name('attendance.manual');
        Route::post('opportunities/{id}/attendance/finalize', [\App\Http\Controllers\Admin\AttendanceAdminController::class, 'finalize'])->name('attendance.finalize');
        Route::post('opportunities/{id}/complete', [\App\Http\Controllers\Admin\AttendanceAdminController::class, 'complete'])->name('opportunities.complete');
    });

use App\Http\Controllers\Admin\OpportunityQRController;

Route::prefix('admin')->as('admin.')->middleware(['web','auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('opportunities/{id}/qr',         [OpportunityQRController::class,'show'])->name('opportunities.qr');
    Route::post('opportunities/{id}/qr/reset',  [OpportunityQRController::class,'reset'])->name('opportunities.qr.reset');
    Route::get('opportunities/{id}/qr/finalize',[OpportunityQRController::class,'finalize'])->name('opportunities.qr.finalize');
    Route::get('opportunities/{id}/qr/issue',   [OpportunityQRController::class,'issue'])->name('opportunities.qr.issue');
});
use App\Http\Controllers\Admin\ApplicationAdminController;
Route::prefix('admin')->as('admin.')->middleware(['web','auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
  Route::get('applications', [ApplicationAdminController::class,'index'])->name('apps.index');
  Route::post('applications/{id}', [ApplicationAdminController::class,'update'])->name('apps.update');
});

Route::middleware(['web','auth','admin'])->prefix('admin')->name('admin.')->group(function () {
});

/* === Canonical Attendance routes (normalized, single source of truth) === */
Route::middleware(['web','auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')->as('admin.')->group(function () {
        Route::get('/opportunities/{opportunity}/attendance', [AttendanceController::class, 'index'])->;
        Route::post('/opportunities/{opportunity}/attendance/{attendance}', [AttendanceController::class, 'update'])->;
        Route::post('/opportunities/{opportunity}/finalize-issue', [AttendanceController::class, 'finalizeIssue'])->;
        Route::post('/certificates/{certificate}/resend', [AttendanceController::class, 'resendCertificate'])->;
    });
