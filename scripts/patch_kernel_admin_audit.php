<?php
$kernel = __DIR__ . '/../app/Http/Kernel.php';
if (!file_exists($kernel)) { echo "[PATCH] Kernel not found.\n"; exit; }
$txt = file_get_contents($kernel);
$bak = $kernel.'.'.date('Ymd_His').'.bak';
copy($kernel, $bak);

// 1) Ensure "use App\Http\Middleware\AdminAuditMiddleware;" exists
if (strpos($txt, 'AdminAuditMiddleware') === false) {
  $txt = preg_replace('/namespace App\\\\Http;\\s+use/m', "namespace App\\Http;\nuse ", $txt, 1);
  $txt = preg_replace('/^namespace App\\\\Http;$/m', "namespace App\\Http;\nuse App\\Http\\Middleware\\AdminAuditMiddleware;", $txt, 1);
  if (strpos($txt, 'use App\\Http\\Middleware\\AdminAuditMiddleware;') === false) {
    $txt = "use App\\Http\\Middleware\\AdminAuditMiddleware;\n".$txt;
  }
}

// 2) Add middleware to web group if not present
if (!preg_match('/AdminAuditMiddleware::class/', $txt)) {
  $txt = preg_replace_callback('/\\$middlewareGroups\\s*=\\s*\\[(.*?)\\];/s', function($m){
    $block = $m[1];
    $block = preg_replace('/(\'web\'\\s*=>\\s*\\[)(.*?)(\\],)/s', function($m2){
      $inner = $m2[2];
      if (strpos($inner, 'AdminAuditMiddleware::class') === false) {
        $inner .= "\n            \\App\\Http\\Middleware\\AdminAuditMiddleware::class,";
      }
      return $m2[1].$inner.$m2[3];
    }, $block, 1);
    return '$middlewareGroups = ['.$block.'];';
  }, $txt, 1);
}

file_put_contents($kernel, $txt);
echo "[PATCH] Kernel patched. Backup: $bak\n";
