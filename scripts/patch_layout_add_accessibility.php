<?php
$path = __DIR__ . '/../resources/views/layouts/app.blade.php';
if (!file_exists($path)) { echo "layouts/app.blade.php not found\n"; exit; }
$c = file_get_contents($path);
if (strpos($c, "partials.accessibility") !== false) { echo "Accessibility include already present.\n"; exit; }
$c = preg_replace(
    '#</body>\s*</html>\s*$#i',
    "    @includeIf('partials.accessibility')\n</body>\n</html>",
    $c, 1, $count
);
if ($count === 0) {
    // Fallback: append at end
    $c .= "\n@includeIf('partials.accessibility')\n";
}
file_put_contents($path, $c);
echo "Accessibility include added.\n";
