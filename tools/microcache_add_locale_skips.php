<?php
$p = "app/Http/Middleware/MicroCache.php";
if (!file_exists($p)) { exit("MicroCache not found (ok)\n"); }
$c = file_get_contents($p);
if (strpos($c, "routeIs('lang.switch'") !== false || strpos($c, "routeIs('locale.switch'") !== false
    || strpos($c, "->routeIs('lang.switch','locale.switch')") !== false
    || strpos($c, "return \$next(\$request); // locale switch skip") !== false) {
  exit("Locale switch already skipped\n");
}
$inject = "        // Skip caching for language switchers\n"
        . "        if (\$request->routeIs('lang.switch','locale.switch')) {\n"
        . "            return \$next(\$request); // locale switch skip\n"
        . "        }\n\n";
$patched = preg_replace('#(public function handle\\([^\\)]*\\)\\s*\\{\\s*)#', "$1$inject", $c, 1, $n);
if ($n) { copy($p, $p.'.bak_locale_skip'); file_put_contents($p, $patched); echo "Patched MicroCache to skip locale switch\n"; }
else { echo "Could not inject skip (structure differs)\n"; }
