<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Public\CertificateVerifyController;

// Public verify
Route::get('/verify', [CertificateVerifyController::class, 'index'])->name('verify.index');
Route::get('/verify/{code}', [CertificateVerifyController::class, 'show'])->name('verify.show');

// Admin certificates
Route::middleware(['web','auth','admin'])->group(function () {
    Route::get('/admin/certificates', [CertificateController::class, 'index'])->name('admin.certificates.index');
    Route::get('/admin/certificates/{id}', [CertificateController::class, 'show'])->name('admin.certificates.show');

    Route::get('/admin/certificates/{id}/whatsapp', [CertificateController::class, 'sendWhatsApp'])->name('admin.certificates.whatsapp');
    Route::post('/admin/certificates/{id}/revoke', [CertificateController::class, 'revoke'])->name('admin.certificates.revoke');
    Route::post('/admin/certificates/{id}/reissue', [CertificateController::class, 'reissue'])->name('admin.certificates.reissue');

    // Generate for a specific opportunity (form posts here)
    Route::post('/admin/opportunities/generate-certs', function(\Illuminate\Http\Request $request) {
        $request->validate(['opportunity_id'=>'required|integer']);
        return app(CertificateController::class)->generateForOpportunity($request, $request->opportunity_id);
    })->name('admin.certificates.generateForOpportunity');
});
