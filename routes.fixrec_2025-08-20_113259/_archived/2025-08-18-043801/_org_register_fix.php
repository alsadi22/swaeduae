<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::middleware(['web','guest'])->group(function () {
    // Show the registration page (renders resources/views/org/register.blade.php)
    Route::view('/org/register', 'org.register')->name('org.register');

    // TEMP submit handler to avoid 500s (replace with real controller later)
    Route::post('/org/register', function (Request $request) {
        Log::info('ORG_REGISTER_STUB', [
            'ip'    => $request->ip(),
            'email' => $request->input('email'),
        ]);
        return back()->with('status', __('Thanks! Your request was received.'));
    })->name('org.register.submit');
});
