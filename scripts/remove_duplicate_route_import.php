<?php
$path = __DIR__ . '/../routes/web.php';
$c = file($path);
if ($c === false) { fwrite(STDERR, "Cannot read web.php\n"); exit(1); }

foreach ($c as $i => $line) {
    if (preg_match('/^use\s+Illuminate\\\\Support\\\\Facades\\\\Route;$/', trim($line))) {
        unset($c[$i]);
    }
}
file_put_contents($path, implode('', $c));
echo "Duplicate Route import removed.\n";
