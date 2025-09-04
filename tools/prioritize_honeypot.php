<?php
$f = 'app/Http/Kernel.php';
$s = file_get_contents($f);
if ($s === false) { fwrite(STDERR,"[ERR] cannot read $f\n"); exit(1); }

$hp  = '\\App\\Http\\Middleware\\Honeypot::class';
$cs  = '\\Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken::class';

if (!preg_match('/protected\s+\$middlewarePriority\s*=\s*\[.*?\];/s', $s)) {
  // Add a priority block with sane defaults + our order
  $block = <<<PHP2

    /**
     * Middleware priority ensures non-global middleware execute in a specific order.
     */
    protected \$middlewarePriority = [
        $hp,
        $cs,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ];
PHP2;
  // Insert before class closing brace
  $s = preg_replace('/}\s*$/', $block."\n}\n", $s, 1, $c);
} else {
  // Ensure HP exists and is before CS
  $s = preg_replace_callback(
    '/protected\s+\$middlewarePriority\s*=\s*\[(.*?)\];/s',
    function($m) use($hp,$cs){
      $list = $m[1];
      // remove any existing occurrences to re-order cleanly
      $list = preg_replace('#\s*\\\\?App\\\\Http\\\\Middleware\\\\Honeypot::class\s*,?#', '', $list);
      // ensure CS exists (it should), then place HP right before it
      $list = preg_replace('#(\\\\Illuminate\\\\Foundation\\\\Http\\\\Middleware\\\\VerifyCsrfToken::class\s*,?)#',
                           $hp.",\n        "."$1", $list, 1, $c);
      if (!$c) { // fallback: just prepend
        $list = "$hp,\n        ".$list;
      }
      return "protected \$middlewarePriority = [\n        ".$list."\n    ];";
    },
    $s, 1, $c
  );
}

copy($f, $f.'.bak_'.date('Ymd_His'));
file_put_contents($f, $s);
echo "[OK] Kernel middleware priority updated (Honeypot before CSRF)\n";
