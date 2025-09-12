<?php
$k = 'app/Http/Kernel.php';
$c = file_get_contents($k);
if ($c === false) { fwrite(STDERR,"Missing $k\n"); exit(1); }

// Ensure use statements
foreach ([
  "use App\\Http\\Middleware\\SecurityHeaders;",
  "use App\\Http\\Middleware\\MicroCache;"
] as $u) {
  if (strpos($c, $u) === false) {
    $c = preg_replace('/^(namespace\s+App\\\\Http;\s*)/m', "$1\n$u\n", $c, 1);
  }
}

// Remove SecurityHeaders from global $middleware if present (we want it LAST in web group)
$c = preg_replace('/^\s*SecurityHeaders::class,\s*$/m', '', $c);

// Helper to insert a line at the end of the 'web' group array if missing
$insertAtEndOfWeb = function(string $code, string $line) {
  if (strpos($code, $line) !== false) return $code;
  if (!preg_match('/\$middlewareGroups\s*=\s*\[/', $code, $m, PREG_OFFSET_CAPTURE)) return $code;
  $mgStart = $m[0][1];

  if (!preg_match('/\'web\'\s*=>\s*\[/', $code, $m2, PREG_OFFSET_CAPTURE, $mgStart)) return $code;
  $webOpen = $m2[0][1];
  $openPos = strpos($code, '[', $webOpen);
  $i = $openPos; $depth = 0; $len = strlen($code);
  for (; $i < $len; $i++) {
    $ch = $code[$i];
    if ($ch === '[') $depth++;
    if ($ch === ']') { $depth--; if ($depth === 0) break; }
  }
  if ($depth !== 0) return $code;

  // Indentation at close
  $closeLineStart = strrpos(substr($code, 0, $i), "\n");
  $indent = '';
  if ($closeLineStart !== false) {
    $lineStr = substr($code, $closeLineStart+1, $i-$closeLineStart-1);
    $indent = preg_replace('/\S.*/', '', $lineStr);
  }
  $deeper = $indent . '    ';
  $beforeClose = rtrim(substr($code, $openPos+1, $i-$openPos-1));
  $needsComma = ($beforeClose !== '' && substr($beforeClose, -1) !== ',');
  $insertion = ($needsComma ? "," : "") . "\n" . $deeper . $line;

  return substr($code, 0, $i) . $insertion . substr($code, $i);
};

// Ensure MicroCache then SecurityHeaders at the tail (SecurityHeaders LAST)
$c = $insertAtEndOfWeb($c, 'MicroCache::class');
$c = $insertAtEndOfWeb($c, 'SecurityHeaders::class');

file_put_contents($k, $c);
echo "Kernel wired: MicroCache then SecurityHeaders at end of web group.\n";
