<?php
$f = 'app/Http/Kernel.php';
$c = file_get_contents($f);
if (strpos($c, "throttle:global") === false) {
    $c = preg_replace(
        "/(protected\\s+\\$middlewareGroups\\s*=\\s*\\[\\s*'web'\\s*=>\\s*\\[)/",
        "$1\n            'throttle:global',",
        $c,
        1
    );
    file_put_contents($f, $c);
    echo "Kernel web group throttled\n";
} else {
    echo "Kernel already throttled\n";
}
