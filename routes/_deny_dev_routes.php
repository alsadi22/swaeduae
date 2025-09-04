<?php
use Illuminate\Support\Facades\Route;
Route::any('_agent{any?}', fn() => abort(404))->where('any','.*');
Route::any('_alias/{any?}', fn() => abort(404));
Route::any('_compat/{any?}', fn() => abort(404));
