<?php
$path = __DIR__ . '/../resources/views/layouts/app.blade.php';
if (!file_exists($path)) { fwrite(STDERR, "layouts/app.blade.php not found\n"); exit(1); }
$c = file_get_contents($path);
$before = $c;
# Remove any @include('partials.lang-toggle') lines (with or without spaces)
$c = preg_replace("/^.*@include\\(['\"]partials\\.lang-toggle['\"]\\).*\\R?/m", '', $c);
if ($c !== $before) {
  file_put_contents($path, $c);
  echo "Removed lang-toggle include from layout.\n";
} else {
  echo "No lang-toggle include found (nothing to remove).\n";
}
