<?php
$f = 'routes/web.php';
if (!is_file($f)) { fwrite(STDERR,"routes/web.php missing\n"); exit(1); }
$src = file_get_contents($f);
$orig = $src;

// 1) Ensure HOME is not named events.show
$src = preg_replace(
    "#Route::get\\s*\\(\\s*['\"]/['\"]\\s*,[^;]+?->name\\s*\\(\\s*['\"]events\\.show['\"]\\s*\\)\\s*;#s",
    "Route::get('/', [\\App\\Http\\Controllers\\HomeController::class, 'index'])->name('home');",
    $src
);

// 2) Force the /events and /events/{idOrSlug} routes to canonical form (one of each)
$src = preg_replace(
    "#Route::get\\s*\\(\\s*['\"]/events['\"]\\s*,[^;]+;#s",
    "Route::get('/events', [\\App\\Http\\Controllers\\EventController::class, 'index'])->name('events.index');",
    $src, 1
);

$src = preg_replace(
    "#Route::get\\s*\\(\\s*['\"]/events/\\{[^}]+\\}['\"]\\s*,[^;]+;#s",
    "Route::get('/events/{idOrSlug}', [\\App\\Http\\Controllers\\EventController::class, 'show'])->name('events.show');",
    $src, 1
);

// If /events/{...} route is missing entirely, append a correct one
if (strpos($src, "->name('events.show')") === false) {
    $src .= "\nRoute::get('/events/{idOrSlug}', [\\App\\Http\\Controllers\\EventController::class, 'show'])->name('events.show');\n";
}

// 3) Remove any duplicate occurrences of events.show beyond the first one
$parts = preg_split("#(->name\\('events\\.show'\\)\\s*;)#s", $src, -1, PREG_SPLIT_DELIM_CAPTURE);
$out = '';
$seen = 0;
for ($i=0; $i<count($parts); $i++) {
    if ($i+1 < count($parts) && $parts[$i+1] === "->name('events.show');") {
        if ($seen++ == 0) { $out .= $parts[$i] . $parts[$i+1]; }
        else { /* skip duplicates */ }
        $i++; // skip the delimiter part already handled
    } else {
        $out .= $parts[$i];
    }
}
$src = $out;

if ($src !== $orig) {
    copy($f, $f.'.bak_'.date('YmdHis'));
    file_put_contents($f, $src);
    echo "routes/web.php normalized\n";
} else {
    echo "No changes needed in routes/web.php\n";
}
