<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::middleware(['web','auth','can:isAdmin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/_diag', function () {
            $u = auth()->user();
            $stats = [
                'users'         => DB::table('users')->count(),
                'events'        => DB::table('events')->count() ?? 0,
                'opportunities' => DB::table('opportunities')->count() ?? 0,
                'applications'  => DB::table('opportunity_applications')->count() ?? 0,
                'qr_scans'      => DB::table('qr_scans')->count() ?? 0,
            ];
            logger()->info('ADMIN_DIAG', ['user' => $u->email ?? null, 'stats' => $stats]);
            return view('admin._diag_dashboard', compact('u','stats'));
        })->name('admin.diag');
    });
