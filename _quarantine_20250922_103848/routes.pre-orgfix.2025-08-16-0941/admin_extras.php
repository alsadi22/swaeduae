<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web','auth', \App\Http\Middleware\AdminMiddleware::class])
    ->group(function () {

        // Media page (sidebar link expects route('admin.media'))
        if (class_exists(\App\Http\Controllers\Admin\MediaController::class)) {
            Route::get('/media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media');
        } else {
            Route::get('/media', function () {
                return view()->exists('admin.media.index')
                    ? view('admin.media.index')
                    : view('admin.placeholder', ['title' => 'Media']);
            })->name('media');
        }

        // Organizations CSV export (index blade uses route('admin.organizations.exportCsv'))
        if (class_exists(\App\Http\Controllers\Admin\OrganizationController::class)
            && method_exists(\App\Http\Controllers\Admin\OrganizationController::class, 'exportCsv')) {

            Route::get('/organizations/export', [\App\Http\Controllers\Admin\OrganizationController::class, 'exportCsv'])
                ->name('organizations.exportCsv');

        } else {
            // Safe fallback CSV stream from DB so the button works today
            Route::get('/organizations/export', function () {
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename=organizations.csv',
                ];
                $callback = function () {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['ID','Name','Email','Owner','Created']);
                    try {
                        foreach (DB::table('organizations')->orderBy('id')->cursor() as $r) {
                            fputcsv($out, [$r->id, $r->name ?? '', $r->email ?? '', $r->owner_id ?? '', $r->created_at ?? '']);
                        }
                    } catch (\Throwable $e) { fputcsv($out, ['error', $e->getMessage()]); }
                    fclose($out);
                };
                return response()->stream($callback, 200, $headers);
            })->name('organizations.exportCsv');
        }
    });
