<?php
$path = __DIR__ . '/../app/Http/Controllers/Org/OpportunityController.php';
$src  = file_get_contents($path);
if ($src === false) { fwrite(STDERR, "Cannot read $path\n"); exit(1); }

$pattern = '/\[\s*\$cols\s*,\s*\$has\s*\]\s*=\s*\$this->eventCols\s*\(\s*\)\s*;/';
$replace = <<<'CODE'
$__ec = $this->eventCols();
$cols  = $__ec['cols'] ?? ($__ec[0] ?? []);
$has   = $__ec['has']  ?? ($__ec[1] ?? []);
CODE;

$dst = preg_replace($pattern, $replace, $src, 1, $count);
if ($count === 0) { fwrite(STDERR, "No match found; file unchanged.\n"); exit(2); }

file_put_contents($path, $dst);
echo "Patched destructuring in OpportunityController.\n";
