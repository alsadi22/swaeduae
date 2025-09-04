<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrgRegistrationController;

// ----- Organization Registration -----
Route::get('organizations/register', [OrgRegistrationController::class, 'create'])->name('organizations.register');
Route::post('organizations/register', [OrgRegistrationController::class, 'store'])->name('organizations.register.store');
Route::get('admin/organizations/pending', [OrgRegistrationController::class, 'pending'])
    ->middleware('auth')->name('admin.organizations.pending');
