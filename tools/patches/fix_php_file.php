<?php
/**
 * Repairs common heredoc/paste damage and brace imbalance without overwriting intent.
 * - Strips injected HTTP headers / "Patched:" / "Restored" noise if accidentally written into code
 * - Normalizes line endings; removes trailing "?>"
 * - Balances braces ignoring strings/comments; trims extra '}' only at EOF if needed
 * - Dedupes "use Illuminate\Support\Facades\Schema;"
 * Makes a timestamped backup: <file>.corrupt_YYYYmmddHHMMSS
 */
if ($argc < 2) { fwrite(STDERR, "Usage: php fix_php_file.php <file> [...]\n"); exit(1); }

function balance_braces($code) {
  $len = strlen($code);
  $opens = 0; $closes = 0;
  $inS = false; $inD = false; $inSL = false; $inML = false; $inH = false; $heredocId = '';
  for ($i=0; $i<$len; $i++) {
    $ch = $code[$i];
    $nx = $i+1<$len?$code[$i+1]:'';
    if ($inSL) { if ($ch === "\n") $inSL = false; continue; }
    if ($inML) { if ($ch==='*' && $nx==='/') { $inML=false; $i++; } continue; }
    if ($inS)  { if ($ch==="'" && $code[$i-1]!=='\\') $inS=false; continue; }
    if ($inD)  { if ($ch=== '"' && $code[$i-1]!=='\\') $inD=false; continue; }

    // start/end comments
    if (!$inH && $ch==='/' && $nx==='/') { $inSL=true; $i++; continue; }
    if (!$inH && $ch==='/' && $nx==='*') { $inML=true; $i++; continue; }

    // strings
    if (!$inH && $ch==="'" && !$inS && !$inD) { $inS=true; continue; }
    if (!$inH && $ch==='"' && !$inS && !$inD) { $inD=true; continue; }

    // heredoc/nowdoc start (very loose)
    if (!$inH && $ch==='<' && substr($code,$i,3)==='<<<') {
      if (preg_match('/^<<<[\'"]?([A-Z_][A-Z0-9_]*)[\'"]?/i', substr($code,$i), $m)) {
        $heredocId = $m[1]; $inH = true;
      }
    }
    if ($inH) {
      if ($ch === "\n") {
        // end marker on own line
        if ($heredocId && preg_match('/^'.preg_quote($heredocId,'/').';?\s*$/', trim(substr($code,$i+1, strlen($heredocId)+2)))) {
          $inH = false; $heredocId = '';
        }
      }
      continue;
    }

    if ($ch === '{') $opens++;
    if ($ch === '}') $closes++;
  }
  return [$opens,$closes];
}

for ($ai=1; $ai<$argc; $ai++) {
  $file = $argv[$ai];
  if (!is_file($file)) { fwrite(STDERR, "Skip: $file (not found)\n"); continue; }
  $orig = file_get_contents($file);
  $code = $orig;

  // 1) Strip injected noise (only if lines are clearly not PHP code)
  $code = preg_replace('/^(X-Powered-By:.*|Content-type:.*|Patched:.*|Restored .*|— end —.*)$/m', '', $code);

  // 2) Normalize line endings and strip trailing "?>"
  $code = str_replace(["\r\n","\r"], "\n", $code);
  $code = preg_replace('/\?>\s*$/', '', $code, 1);

  // 3) Remove duplicate "use Schema" lines
  $code = preg_replace('/^(use\s+Illuminate\\\\Support\\\\Facades\\\\Schema;\s*){2,}/m', 'use Illuminate\\Support\\Facades\\Schema;'."\n", $code);

  // 4) Quick fix for accidental duplicated "}\n}" at EOF
  $code = preg_replace('/\n\}\s*\}\s*$/', "\n}\n", $code, 1);

  // 5) Balance braces ignoring strings/comments
  [$o,$c] = balance_braces($code);
  if ($o > $c) {
    $code .= str_repeat("}\n", $o - $c);
  } elseif ($c > $o) {
    // remove extra closing braces only from EOF region
    $extras = $c - $o;
    for ($k=0; $k<$extras; $k++) { $code = preg_replace('/\}\s*$/', '', $code, 1); }
    $code .= "\n";
  }

  if ($code !== $orig) {
    copy($file, $file.'.corrupt_'.date('YmdHis'));
    file_put_contents($file, $code);
    echo "Fixed: $file\n";
  } else {
    echo "No change: $file\n";
  }
}
