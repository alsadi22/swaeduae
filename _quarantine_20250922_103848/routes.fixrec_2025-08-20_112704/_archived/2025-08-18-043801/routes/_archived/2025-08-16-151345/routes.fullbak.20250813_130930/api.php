<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::prefix('v1')->group(function () {
    Route::get('/events', function () {
        return DB::table('events')->select('id','title','location','date','description')->orderByDesc('id')->paginate(20);
    });
    Route::get('/opportunities', function () {
        return DB::table('opportunities')->select('id','title','location','start_date','end_date','description','seats','slug')->orderByDesc('id')->paginate(20);
    });
    Route::get('/verify/{code}', function ($code) {
        $c = DB::table('certificates')->where('code',$code)->first();
        return [
            'valid'=>(bool)$c,
            'certificate'=>$c,
        ];
    });
});
