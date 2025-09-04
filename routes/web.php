// == Admin Approvals Console ==
use App\Http\Controllers\Admin\ApprovalsController;
Route::middleware(['web','auth','can:admin-access'])->prefix('admin')->name('admin.')->group(function () {
  Route::get('/approvals', [ApprovalsController::class, 'index'])->name('approvals.index');
  Route::post('/approvals/orgs/{id}/approve', [ApprovalsController::class, 'approveOrg'])->name('approvals.orgs.approve');
  Route::post('/approvals/orgs/{id}/deny',    [ApprovalsController::class, 'denyOrg'])->name('approvals.orgs.deny');
  Route::post('/approvals/apps/{id}/approve', [ApprovalsController::class, 'approveApp'])->name('approvals.apps.approve');
  Route::post('/approvals/apps/{id}/deny',    [ApprovalsController::class, 'denyApp'])->name('approvals.apps.deny');
});
