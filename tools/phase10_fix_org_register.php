<?php
$it = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator('routes', FilesystemIterator::SKIP_DOTS)
);
$total=0; $changed=0;
foreach ($it as $p) {
  if ($p->getExtension() !== 'php') continue;
  $path = $p->getPathname();
  if (strpos($path, '/_archived/') !== false || strpos($path, '/_disabled/') !== false) continue;

  $s = file_get_contents($path);
  $orig = $s;

  // Find any "Route::post('org/register' ... ;" chunk and append middleware if not present
  $s = preg_replace_callback('/Route::post\(\s*[\'"]org\/register[\'"][^;]*;/s', function($m){
      $chunk = $m[0];
      if (preg_match('/honeypot|Honeypot/', $chunk)) return $chunk;
      return preg_replace('/;$/', "->middleware(\\App\\Http\\Middleware\\Honeypot::class);", $chunk);
  }, $s, -1, $c1);

  if ($c1) {
    copy($path, $path.'.bak_'.date('Ymd_His'));
    file_put_contents($path, $s);
    echo "[FIXED] $path (org/register:+honeypot)\n";
    $changed++;
  }
  $total++;
}
echo "[DONE] scanned:$total changed:$changed\n";
