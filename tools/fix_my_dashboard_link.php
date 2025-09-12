<?php
$f = 'resources/views/argon_front/_navbar.blade.php';
if (!is_file($f)) { fwrite(STDERR,"Not found: $f\n"); exit(1); }
$s = file_get_contents($f);

/* Replace href="{{ url('/profile') }}" used with the My Dashboard anchor */
$pattern = '~(<a[^>]*href=)("\{\{\s*url\([\'"]/profile[\'"]\)\s*\}\}")([^>]*>\s*@lang\([\'"]My Dashboard[\'"]\)\s*</a>)~';
$replacement = '$1"{{ (auth()->check() && (method_exists(auth()->user(),\'hasRole\') ? auth()->user()->hasRole(\'org\') : request()->is(\'org*\'))) ? route(\'org.dashboard\') : route(\'profile\') }}"$3';

$out = preg_replace($pattern, $replacement, $s, 1, $cnt);
if ($cnt) {
  file_put_contents($f, $out);
  echo "Patched $f (made My Dashboard role-aware)\n";
} else {
  echo "Pattern not found in $f (it may already be conditional or the file differs)\n";
}
