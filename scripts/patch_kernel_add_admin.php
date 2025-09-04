<?php
$path = __DIR__ . '/../app/Http/Kernel.php';
$src = file_get_contents($path);
if ($src === false) { fwrite(STDERR, "Cannot read Kernel.php\n"); exit(1); }

if (strpos($src, "'admin' => \\App\\Http\\Middleware\\AdminMiddleware::class") === false) {
    $src = preg_replace(
        '/protected\\s+\\$routeMiddleware\\s*=\\s*\\[(.*?)(\\];)/s',
        function($m){
            $block = $m[1];
            // Insert before closing bracket, keep formatting light
            $insertion = "\n        'admin' => \\App\\Http\\Middleware\\AdminMiddleware::class,";
            // Avoid duplicate commas/newlines mess
            return "protected \$routeMiddleware = [".$block.$insertion."\n    ".$m[2];
        },
        $src,
        1
    );
    file_put_contents($path, $src);
    echo "Added 'admin' middleware alias to Kernel.php\n";
} else {
    echo "'admin' middleware alias already present.\n";
}
