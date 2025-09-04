<?php
function attach($path, $lines, $onlyTag) {
  if (!file_exists($path)) { echo "[SKIP] $path (missing)\n"; return; }
  $s=file_get_contents($path); $orig=$s;

  // Already present?
  if (strpos($s, "Honeypot::class")!==false && strpos($s, $onlyTag)!==false) {
    echo "[SKIP] $path (already has honeypot for $onlyTag)\n"; return;
  }

  // Ensure use statement for Honeypot if not using FQCN everywhere (we use FQCN below, so not needed)

  // Has a constructor?
  if (preg_match('/function\s+__construct\s*\([^)]*\)\s*\{/', $s, $m, PREG_OFFSET_CAPTURE)) {
    $pos = $m[0][1] + strlen($m[0][0]);
    $s = substr($s,0,$pos) . "\n        ".$lines."\n" . substr($s,$pos);
  } else {
    // Insert a constructor after the class opening brace
    if (preg_match('/class\s+[A-Za-z0-9_\\\\]+\s*[^{]*\{/', $s, $m, PREG_OFFSET_CAPTURE)) {
      $pos = $m[0][1] + strlen($m[0][0]);
      $ctor = "\n    public function __construct()\n    {\n        ".$lines."\n    }\n";
      $s = substr($s,0,$pos) . $ctor . substr($s,$pos);
    } else {
      echo "[WARN] $path (no class brace found)\n"; return;
    }
  }

  copy($path, $path.'.bak_'.date('Ymd_His'));
  file_put_contents($path,$s);
  echo "[OK] Patched $path\n";
}

attach('app/Http/Controllers/Auth/LoginController.php',
       "\$this->middleware([\\App\\Http\\Middleware\\Honeypot::class, 'throttle:login'])->only('login');",
       "only('login')");
attach('app/Http/Controllers/Auth/RegisterController.php',
       "\$this->middleware(\\App\\Http\\Middleware\\Honeypot::class)->only('register');",
       "only('register')");
attach('app/Http/Controllers/Admin/Auth/AdminLoginController.php',
       "\$this->middleware([\\App\\Http\\Middleware\\Honeypot::class, 'throttle:login'])->only('login');",
       "only('login')");
