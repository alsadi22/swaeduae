<?php
/** Canonical host redirect: apex -> www (keeps path) */
Route::domain('swaeduae.ae')->any('/{any?}', function (?string $any = null) {
    $path = $any ? '/'.ltrim($any, '/') : '';
    return redirect()->away('https://www.swaeduae.ae'.$path, 301);
})->where('any', '.*');
