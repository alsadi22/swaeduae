<?php
$path = __DIR__ . '/../app/Http/Kernel.php';
$c = file_get_contents($path);
if (strpos($c, "=> \\App\\Http\\Middleware\\EnsureOrg::class") !== false) {
    echo "Kernel alias already present.\n"; exit;
}
$c = preg_replace_callback(
    '/protected\\s+\\$middlewareAliases\\s*=\\s*\\[\\s*/',
    function($m){
        return $m[0]."        'org' => \\App\\Http\\Middleware\\EnsureOrg::class,\n";
    },
    $c, 1, $count
);
if ($count === 0) { echo "Could not patch Kernel.php; add alias manually.\n"; exit(1); }
file_put_contents($path, $c);
echo "Kernel alias added.\n";
