#!/usr/bin/env bash
set -euo pipefail
export LC_ALL=C

OUT="${HOME}/swaed_scans/site_inventory/$(date +%Y%m%d_%H%M%S)"
mkdir -p "$OUT"

echo "==[1/5] routes -> JSON =="
if php artisan route:list --json > "$OUT/routes.json" 2>/dev/null; then
  echo "routes.json written"
else
  php artisan route:list > "$OUT/routes.txt"
  echo "no JSON; wrote routes.txt"
fi

echo "==[2/5] build URL list (GET/HEAD, optional params only) =="
php -d detect_unicode=0 -r '
$in = file_exists($argv[1]."/routes.json") ? json_decode(file_get_contents($argv[1]."/routes.json"), true) : [];
$out = fopen($argv[1]."/routes.urls.tsv", "w");
$skip = "#^/(?:_(?:agent|alias|compat))$#";
foreach ($in as $r) {
  $m = strtoupper($r["method"] ?? "");
  $methods = array_map("trim", explode("|", $m));
  if (!in_array("GET",$methods) && !in_array("HEAD",$methods)) continue;
  $domain = $r["domain"] ?? "";
  $uri = "/".ltrim($r["uri"] ?? "", "/");
  if (preg_match_all("#\\{([^\\}]+)\\}#", $uri, $ms)) {
    $bad=false; foreach ($ms[1] as $p) { if (substr($p,-1)!=="?") { $bad=true; break; } }
    if ($bad) continue;
    $uri = preg_replace("#/\\{[^/]+\\?\\}#", "", $uri);
    $uri = preg_replace("#\\{[^}]+\\?\\}#", "", $uri);
  }
  $uri = preg_replace("#(?<!:)//+#", "/", $uri);
  if (preg_match($skip, $uri)) continue;
  $host = (strpos($domain, "admin.swaeduae.ae") !== false) ? "https://admin.swaeduae.ae" : "https://swaeduae.ae";
  $url = $host . $uri;
  fwrite($out, ($methods[0] ?? "GET")."\t".($r["name"]??"")."\t".$url."\n");
}
fclose($out);
' "$OUT"

echo "==[3/5] probe URLs =="
UA="Mozilla/5.0 (X11; Linux x86_64) curl-inventory"
PUB_OPTS=(-A "$UA" --retry 2 --retry-all-errors --connect-timeout 6 --max-time 20 -sSL -H "Accept: */*")
ADM_OPTS=("${PUB_OPTS[@]}")
[ -n "${ADMIN_RESOLVE_IP:-}" ] && ADM_OPTS+=(--resolve "admin.swaeduae.ae:443:${ADMIN_RESOLVE_IP}")
[ "${ADMIN_INSECURE:-0}" = 1 ] && ADM_OPTS+=(-k)

> "$OUT/url_status.tsv"
while IFS=$'\t' read -r METHOD NAME URL; do
  [[ -z "${URL:-}" ]] && continue
  METHOD_CLEAN="${METHOD%%|*}"
  if [[ "$URL" == https://admin.swaeduae.ae* ]]; then
    CODE=$(curl "${ADM_OPTS[@]}" -o /dev/null -w "%{http_code}" -X "$METHOD_CLEAN" "$URL" || echo 000)
  else
    CODE=$(curl "${PUB_OPTS[@]}" -o /dev/null -w "%{http_code}" -X "$METHOD_CLEAN" "$URL" || echo 000)
  fi
  printf "%s\t%s\t%s\t%s\n" "$CODE" "$METHOD" "$NAME" "$URL" >> "$OUT/url_status.tsv"
done < "$OUT/routes.urls.tsv"

awk -F'\t' 'BEGIN{OFS="\t"} {c=$1; if(c~/^(200|201|202|204|301|302|303|307|308|401|403)$/) print > "'"$OUT"'/url_ok.tsv"; else print > "'"$OUT"'/url_bad.tsv"}' "$OUT/url_status.tsv" || true

echo "==[4/5] view references <-> blade existence =="
php -r '
function collect($d,$pat){
  $it=[]; $rii=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d));
  foreach($rii as $f){ if(!$f->isFile()) continue; $n=$f->getPathname(); if(!preg_match($pat,$n)) continue;
    $s=@file_get_contents($n); if($s===false) continue;
    if(preg_match_all("/view\\([\\x27\\\"][A-Za-z0-9_\\.\\/-]+[\\x27\\\"]\\)/",$s,$m))
      foreach($m[0] as $v) if(preg_match("/view\\([\\x27\\\"]([^\\x27\\\"]+)[\\x27\\\"]\\)/",$v,$mm)) $it[$mm[1]]=1;
    if(preg_match_all("/@(include|includeIf|extends|component)\\([\\x27\\\"][A-Za-z0-9_\\.\\/-]+[\\x27\\\"]\\)/",$s,$m))
      foreach($m[0] as $v) if(preg_match("/@(?:include|includeIf|extends|component)\\([\\x27\\\"]([^\\x27\\\"]+)[\\x27\\\"]\\)/",$v,$mm)) $it[$mm[1]]=1;
  } return array_keys($it);
}
$out = $argv[1];
$ref = collect("app","/(\\.php)$/");
$ref = array_merge($ref, collect("routes","/(\\.php)$/"));
$ref = array_merge($ref, collect("resources/views","/(\\.blade\\.php)$/"));
$ref = array_values(array_unique($ref));
file_put_contents($out."/views.referenced.txt", join(PHP_EOL,$ref));
' "$OUT"

# ensure both lists sorted before comm
sort -u "$OUT/views.referenced.txt" -o "$OUT/views.referenced.txt"

> "$OUT/views.missing.tsv"; > "$OUT/views.ok.tsv"
while IFS= read -r V; do
  [[ -z "$V" ]] && continue
  P="resources/views/${V//./\/}.blade.php"
  if [[ -f "$P" ]]; then printf "OK\t%s\t%s\n" "$V" "$P" >> "$OUT/views.ok.tsv"
  else printf "MISSING\t%s\t%s\n" "$V" "$P" >> "$OUT/views.missing.tsv"
  fi
done < "$OUT/views.referenced.txt"

find resources/views -type f -name '*.blade.php' | sed 's#^resources/views/##; s#\.blade\.php$##; s#/#.#g' | sort -u > "$OUT/views.all.txt"
comm -23 "$OUT/views.all.txt" "$OUT/views.referenced.txt" \
 | grep -Ev '(^partials\.|^components\.|^vendor\.|\.modal$|\.item$|^layouts?\.)' > "$OUT/views.orphans.txt" || true

echo "==[5/5] summary =="
{
  echo "# URL status (bad)"; [ -f "$OUT/url_bad.tsv" ] && column -t -s $'\t' "$OUT/url_bad.tsv" || echo "(none)";
  echo; echo "# Missing views"; [ -s "$OUT/views.missing.tsv" ] && column -t -s $'\t' "$OUT/views.missing.tsv" || echo "(none)";
  echo; echo "# Orphan views (top 20)"; head -20 "$OUT/views.orphans.txt" || true;
} > "$OUT/SUMMARY.txt"

echo "== DONE =="; echo "Reports: $OUT"; ls -1 "$OUT"
