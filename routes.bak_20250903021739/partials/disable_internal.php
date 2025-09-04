<?php

use Illuminate\Support\Facades\Route;

if (app()->environment('production')) {
    Route::any('/_agent{any?}', fn() => abort(404))->where('any','.*');
    Route::any('/_alias/{any?}', fn() => abort(404))->where('any','.*');
    Route::any('/_compat/{any?}', fn() => abort(404))->where('any','.*');
}
