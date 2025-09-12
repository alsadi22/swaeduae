<?php
$k = 'app/Http/Kernel.php';
if (!file_exists($k)) { fwrite(STDERR,"Missing $k\n"); exit(1); }
$c = file_get_contents($k);

// Ensure use statements after namespace
$ensureUse = function(string $code, string $use) {
  if (strpos($code, $use) !== false) return $code;
  return preg_replace('/^(namespace\s+App\\\\Http;\s*)/m', "$1\n$use\n", $code, 1);
};
$c = $ensureUse($c, 'use App\Http\Middleware\SetLocaleAndHeaders;');
$c = $ensureUse($c, 'use App\Http\Middleware\MicroCache;');

// Helper to insert into a PHP array property block safely
$insertIntoArrayProp = function(string $code, string $propName, string $lineToAdd) {
  if (strpos($code, $lineToAdd) !== false) return $code;
  $start = preg_match('/\$(' . preg_quote($propName, '/') . ')\s*=\s*\[/', $code, $m, PREG_OFFSET_CAPTURE);
  if (!$start) return $code; // property missing; do nothing
  $pos = $m[0][1];
  $openPos = strpos($code, '[', $pos);
  $i = $openPos;
  $depth = 0;
  $len = strlen($code);
  for (; $i < $len; $i++) {
    $ch = $code[$i];
    if ($ch === '[') $depth++;
    if ($ch === ']') { $depth--; if ($depth === 0) { break; } }
  }
  if ($depth !== 0) return $code; // unbalanced
  // Insert before the closing bracket, preserving indentation
  // Find indentation of the closing bracket line
  $closeLineStart = strrpos(substr($code, 0, $i), "\n");
  $indent = '';
  if ($closeLineStart !== false) {
    $line = substr($code, $closeLineStart+1, $i-$closeLineStart-1);
    $indent = preg_replace('/\S.*/', '', $line);
  }
  // Also determine one level deeper indent
  $deeper = $indent . '    ';
  // If the previous non-whitespace before ']' is a comma? ensure trailing comma
  $beforeClose = rtrim(substr($code, $openPos+1, $i-$openPos-1));
  $needsComma = ($beforeClose !== '' && substr($beforeClose, -1) !== ',');
  $insertion = ($needsComma ? "," : "") . "\n" . $deeper . $lineToAdd;
  return substr($code, 0, $i) . $insertion . substr($code, $i);
};

// 1) Global middleware: SetLocaleAndHeaders
$c = $insertIntoArrayProp($c, 'middleware', 'SetLocaleAndHeaders::class');

// 2) web group tail: MicroCache
// We’ll find the 'web' => [ ... ] block and add MicroCache::class if missing
if (strpos($c, 'MicroCache::class') === false) {
  if (preg_match('/(\$middlewareGroups\s*=\s*\[)/', $c, $m, PREG_OFFSET_CAPTURE)) {
    $mgStart = $m[0][1];
    // Locate "'web' => ["
    if (preg_match('/\'web\'\s*=>\s*\[/', $c, $m2, PREG_OFFSET_CAPTURE, $mgStart)) {
      $webOpen = $m2[0][1];
      $openPos = strpos($c, '[', $webOpen);
      $i = $openPos;
      $depth = 0; $len = strlen($c);
      for (; $i < $len; $i++) {
        $ch = $c[$i];
        if ($ch === '[') $depth++;
        if ($ch === ']') { $depth--; if ($depth === 0) break; }
      }
      if ($depth === 0) {
        // compute indentation at close
        $closeLineStart = strrpos(substr($c, 0, $i), "\n");
        $indent = '';
        if ($closeLineStart !== false) {
          $line = substr($c, $closeLineStart+1, $i-$closeLineStart-1);
          $indent = preg_replace('/\S.*/', '', $line);
        }
        $deeper = $indent . '    ';
        // Check comma before close
        $beforeClose = rtrim(substr($c, $openPos+1, $i-$openPos-1));
        $needsComma = ($beforeClose !== '' && substr($beforeClose, -1) !== ',');
        $insertion = ($needsComma ? "," : "") . "\n" . $deeper . "MicroCache::class";
        $c = substr($c, 0, $i) . $insertion . substr($c, $i);
      }
    }
  }
}

file_put_contents($k, $c);
echo "Kernel patched successfully.\n";
