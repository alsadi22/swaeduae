<?php
$path = __DIR__ . '/../resources/views/layouts/app.blade.php';
if (!file_exists($path)) { fwrite(STDERR, "layouts/app.blade.php not found\n"); exit(1); }
$c = file_get_contents($path);
if (strpos($c, "partials.lang-toggle") !== false) { echo "Lang toggle already included.\n"; exit(0); }
$c = str_replace('</body>', "  @include('partials.lang-toggle')\n</body>", $c);
file_put_contents($path, $c);
echo "Lang toggle injected into layout.\n";
