<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/me/applications', function () {
        $rows = DB::table('applications')
            ->join('opportunities', 'opportunities.id', '=', 'applications.opportunity_id')
            ->select('applications.*', 'opportunities.title')
            ->where('applications.user_id', auth()->id())
            ->latest('applications.created_at')
            ->paginate(12);

        return view('me.applications', compact('rows'));
    })->name('me.applications');
});


Route::middleware(['auth','verified'])->group(function () {
    Route::get('/me/certificates', function () {
        $rows = DB::table('certificates')
            ->where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(12);

        return view('me.certificates', compact('rows'));
    })->name('me.certificates');
});
