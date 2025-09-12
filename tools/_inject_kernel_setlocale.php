<?php
$k = "app/Http/Kernel.php";
$s = file_get_contents($k);
if (strpos($s, "'setlocale'") === false) {
  $s = preg_replace("/(protected \\$middlewareAliases\\s*=\\s*\\[)/", "$1\n        'setlocale' => \\\\App\\\\Http\\\\Middleware\\\\SetLocale::class,", $s, 1);
}
if (strpos($s, "\\App\\Http\\Middleware\\SetLocale::class") === false) {
  $s = preg_replace("/('web'\\s*=>\\s*\\[)(.*?)(\\n\\s*\\],)/s", "$1$2\n            \\\\App\\\\Http\\\\Middleware\\\\SetLocale::class,$3", $s, 1);
}
file_put_contents($k, $s);
echo "[OK] Kernel patched for SetLocale\n";
