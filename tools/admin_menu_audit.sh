#!/usr/bin/env bash
set -euo pipefail

MENU_FILE="${1:-resources/views/admin/argon/_sidenav.blade.php}"
echo "Analyzing: $MENU_FILE"
[ -f "$MENU_FILE" ] || { echo "Menu blade not found"; exit 1; }

php artisan tinker --execute='
use Illuminate\Support\Facades\Route;

$menu = $argv[1] ?? "resources/views/admin/argon/_sidenav.blade.php";
$src  = @file_get_contents($menu) ?: "";

echo "\n-- route() calls in $menu --\n";
preg_match_all("/route\([\'\"]([^\'\"]+)[\'\"][^\)]*\)/", $src, $m);
$names = array_values(array_unique($m[1] ?? []));
if (!$names) { echo "(none)\n"; }
foreach ($names as $n) {
    $r = Route::getRoutes()->getByName($n);
    $ok = $r ? "OK" : "MISSING";
    $uri = $r ? $r->uri() : "-";
    $methods = $r ? implode(",", $r->methods()) : "-";
    printf("%-28s  %-8s  %-40s  %s\n", $n, $ok, $uri, $methods);
}

echo "\n-- hardcoded hrefs in $menu --\n";
preg_match_all("/href\\s*=\\s*\"([^\"]+)\"/i", $src, $h);
$hrefs = array_values(array_unique($h[1] ?? []));
if (!$hrefs) { echo "(none)\n"; }
foreach ($hrefs as $href) {
    // skip blade expressions like href="{{ route(...) }}"
    if (strpos($href,"{{") !== false) continue;
    echo $href, "\n";
}

echo "\n-- all admin route names present --\n";
foreach (Route::getRoutes() as $r) {
    $name = $r->getName();
    $uri  = $r->uri();
    if ($name && (str_starts_with($uri, "admin") || str_starts_with((string)$name,"admin."))) {
        printf("%-32s  %s\n", $name, $uri);
    }
}
' -- "$MENU_FILE"
