<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->get('/whoami', function () {
    $u = auth()->user();
    return response()->json([
        'id'    => $u?->id,
        'email' => $u?->email,
        'roles' => $u?->getRoleNames()?->toArray(),
    ]);
});
