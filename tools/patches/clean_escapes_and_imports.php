<?php
/**
 * Fixes heredoc/paste damage:
 * - Replace "\$" with "$" (escaped vars from earlier patches)
 * - Replace "use Illuminate\Support\Facades\Schema as Schema;" with "use Illuminate\Support\Facades\Schema;"
 * - Dedupe "use Illuminate\Support\Facades\Schema;" lines
 * - If file is a Controller, remove any Schema import (controller shouldn't need it)
 * - Remove trailing "?>", normalize line endings
 * Makes a backup: <file>.fixbak_YYYYmmddHHMMSS
 */
if ($argc < 2) { fwrite(STDERR, "Usage: php clean_escapes_and_imports.php <file> [...]\n"); exit(1); }
foreach (array_slice($argv,1) as $file) {
  if (!is_file($file)) { fwrite(STDERR,"Skip: $file (not found)\n"); continue; }
  $orig = file_get_contents($file);
  $src  = str_replace(["\r\n","\r"], "\n", $orig);

  // 1) Fix escaped variables produced by earlier writer scripts
  $src = preg_replace('/\\\\\$/', '$', $src);

  // 2) Normalize Schema import
  $src = preg_replace(
    '/^use\s+Illuminate\\\\Support\\\\Facades\\\\Schema\s+as\s+Schema\s*;\s*$/mi',
    "use Illuminate\\Support\\Facades\\Schema;",
    $src
  );

  // 3) Dedupe multiple identical "use ...Schema;" lines
  $lines = explode("\n", $src);
  $seenSchema = false;
  foreach ($lines as $i => $line) {
    if (preg_match('/^use\s+Illuminate\\\\Support\\\\Facades\\\\Schema\s*;\s*$/', $line)) {
      if ($seenSchema) { $lines[$i] = ''; } else { $seenSchema = true; }
    }
  }
  $src = implode("\n", $lines);

  // 4) If it's a controller, remove Schema import entirely
  if (stripos($file, '/Http/Controllers/') !== false) {
    $src = preg_replace('/^use\s+Illuminate\\\\Support\\\\Facades\\\\Schema\s*;\s*$/mi', '', $src);
  }

  // 5) Remove trailing PHP close tag if present
  $src = preg_replace('/\?>\s*$/', '', $src, 1);

  if ($src !== $orig) {
    copy($file, $file.'.fixbak_'.date('YmdHis'));
    file_put_contents($file, $src);
    echo "Cleaned: $file\n";
  } else {
    echo "No change: $file\n";
  }
}
