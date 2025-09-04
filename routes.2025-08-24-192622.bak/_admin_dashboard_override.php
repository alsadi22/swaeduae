<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

Route::middleware(['web','auth','can:isAdmin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        try {
            $stats = [
                'users'          => DB::table('users')->count(),
                'events'         => DB::table('events')->count() ?? 0,
                'opportunities'  => DB::table('opportunities')->count() ?? 0,
                'applications'   => DB::table('opportunity_applications')->count() ?? 0,
                'qr_scans'       => DB::table('qr_scans')->count() ?? 0,
            ];
            // Try the original blade first; if it throws, we catch and render the safe one.
            return view('admin.dashboard', compact('stats'));
        } catch (\Throwable $e) {
            Log::error('ADMIN_DASH error', ['m'=>$e->getMessage(),'f'=>$e->getFile(),'l'=>$e->getLine()]);
            $error = ['m'=>$e->getMessage(),'f'=>$e->getFile(),'l'=>$e->getLine()];
            // Rebuild lightweight stats just in case previous step failed mid-way
            $stats = [
                'users'          => DB::table('users')->count(),
                'events'         => DB::table('events')->count() ?? 0,
                'opportunities'  => DB::table('opportunities')->count() ?? 0,
                'applications'   => DB::table('opportunity_applications')->count() ?? 0,
                'qr_scans'       => DB::table('qr_scans')->count() ?? 0,
            ];
            return view('admin.dashboard_safe', compact('stats','error'));
        }
    })->name('admin.dashboard'); // overrides existing definition safely
});
