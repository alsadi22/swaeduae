
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OpportunityController;
use App\Http\Controllers\Admin\QrVerifyController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::domain(env('ADMIN_DOMAIN', 'admin.swaeduae.ae'))
    ->middleware(['auth', 'can:admin-access'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        Route::get('/_whoami', function () {
            $u = auth()->user();

            return response()->json(['id' => $u?->id, 'role' => $u->role ?? null]);
        })->name('diag.whoami');
        Route::get('/hours/export', [\App\Http\Controllers\Admin\HoursReportController::class, 'exportCsv'])->name('hours.export');
        Route::get('/applicants/export', [\App\Http\Controllers\Admin\ApplicantsController::class, 'exportCsv'])->name('applicants.export');
        Route::post('/organizations/{id}/suspend', [\App\Http\Controllers\Admin\OrganizationsController::class, 'suspend'])->name('organizations.suspend');
        Route::post('/organizations/{id}/approve', [\App\Http\Controllers\Admin\OrganizationsController::class, 'approve'])->name('organizations.approve');
        Route::get('/organizations', [\App\Http\Controllers\Admin\OrganizationsController::class, 'index'])->name('organizations.index');
        Route::post('/settings/save', [\App\Http\Controllers\Admin\SettingsController::class, 'save'])->name('settings.save');
        Route::get('/reports/export', [\App\Http\Controllers\Admin\ReportsController::class, 'export'])->name('reports.export');
        Route::post('/certificates/issue', [\App\Http\Controllers\Admin\CertificateController::class, 'issue'])->name('certificates.issue');
        Route::post('/certificates/{id}/reissue', [\App\Http\Controllers\Admin\CertificateController::class, 'reissue'])->name('certificates.reissue');
        Route::post('/hours/bulk-approve', [\App\Http\Controllers\Admin\HoursReportController::class, 'bulkApprove'])->name('hours.bulkApprove');
        Route::post('/applicants/bulk', [\App\Http\Controllers\Admin\ApplicantsController::class, 'bulk'])->name('applicants.bulk');
        Route::post('/applicants/{id}/approve', [\App\Http\Controllers\Admin\ApplicantsController::class, 'approve'])->name('applicants.approve');
        Route::post('/applicants/{id}/decline', [\App\Http\Controllers\Admin\ApplicantsController::class, 'decline'])->name('applicants.decline');
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
        Route::get('/certificates', [\App\Http\Controllers\Admin\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceAdminController::class, 'index'])->name('attendance.index');
        Route::get('/applicants', [\App\Http\Controllers\Admin\ApplicantsController::class, 'index'])->name('applicants.index');
        Route::get('/qr/verify', [QrVerifyController::class, 'index'])->name('qr.verify');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::resource('opportunities', OpportunityController::class)->only(['index', 'show', 'update', 'destroy']);
    });
