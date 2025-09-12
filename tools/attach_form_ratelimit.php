<?php
$targets = ['register.perform','org.register.store'];
$dir = __DIR__.'/../routes';
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($rii as $file) {
    if ($file->isDir() || $file->getExtension() !== 'php') continue;
    $path = $file->getPathname();
    $code = file_get_contents($path);
    $orig = $code;
    foreach ($targets as $name) {
        // if already present, skip
        if (preg_match("#name\\(['\\\"]{$name}['\\\"]\\).*form\\.ratelimit#s", $code)) continue;
        // case A: already has ->middleware([...])
        $code = preg_replace(
            "#(Route::post\\([^;]+?->middleware\\(\\[)([^\\]]*?)(\\]\\)->name\\(['\\\"]{$name}['\\\"]\\))#s",
            "$1$2, 'form.ratelimit'$3",
            $code
        );
        // case B: add new ->middleware([...]) before ->name(...)
        $code = preg_replace(
            "#(Route::post\\([^;]+?)(->name\\(['\\\"]{$name}['\\\"]\\))#s",
            "$1->middleware(['form.ratelimit'])$2",
            $code
        );
    }
    if ($code !== $orig) {
        copy($path, $path.'.bak');
        file_put_contents($path, $code);
        echo \"Patched: $path\\n\";
    }
}
