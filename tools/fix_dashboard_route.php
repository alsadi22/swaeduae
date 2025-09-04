<?php
$f = 'routes/web.php';
$src = file_get_contents($f);
$bak = $f.'.bak_'.date('Ymd_His');
copy($f, $bak);

$dynamic = <<<'ROUTE'
Route::get('/dashboard', function () {
    $u = auth()->user();
    if ($u && (
        (method_exists($u,'hasRole') && $u->hasRole('org')) ||
        (method_exists($u,'isOrg') && $u->isOrg()) ||
        request()->is('org*')
    )) {
        return redirect()->route('org.dashboard');
    }
    return redirect()->route('profile');
})->name('dashboard');
ROUTE;

$changed = false;

/* Replace the old permanent redirect if present */
$re = "~Route::permanentRedirect\('/dashboard'\s*,\s*'/profile'\)\s*->name\('redir\.dashboard\.profile'\);~";
if (preg_match($re, $src)) {
    $src = preg_replace($re, $dynamic, $src, 1, $cnt);
    $changed = $changed || ($cnt > 0);
}

/* If there's no /dashboard at all, append a new one */
if (!preg_match("~\\bRoute::(get|redirect|permanentRedirect)\\('/dashboard'~", $src)) {
    $src .= "\n\n".$dynamic."\n";
    $changed = true;
}

file_put_contents($f, $src);
echo $changed ? "Updated routes/web.php\n" : "No change needed\n";
