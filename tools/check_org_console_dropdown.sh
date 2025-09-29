#!/usr/bin/env bash
set -euo pipefail
echo "=== ORG CONSOLE DROPDOWN DIAGNOSTIC ==="

LAY="resources/views/org/layout.blade.php"
NAV="resources/views/admin/argon/_navbar.blade.php"

echo -e "\n[1] Which layout do org pages use?"
grep -RIn --include='*.blade.php' -E "^@extends\(['\"]org\.layout['\"]\)" resources/views/org | wc -l | awk '{print "  org pages extending org.layout: "$1}'

echo -e "\n[2] org/layout.blade.php: head + our CSS block presence"
grep -n "@includeIf('admin.argon._navbar')" "$LAY" || echo "  (navbar include NOT found)"
grep -n "@includeIf('admin.argon._footer')" "$LAY" || echo "  (footer include NOT found)"
grep -n "org-menu-minimal:start" "$LAY" || echo "  (org-menu-minimal CSS block NOT found)"
echo "  Extracted CSS rules (normalized):"
awk '/org-menu-minimal:start/,/org-menu-minimal:end/ {print}' "$LAY" | sed -E 's/[[:space:]]+/ /g;s/^ //'

echo -e "\n[3] Does the navbar define the dropdown correctly?"
if [ -f "$NAV" ]; then
  grep -n "Organization Console" "$NAV" || echo "  (label not in file — ok if translatable)"
  echo "  First dropdown-menu line(s):"
  grep -n "dropdown-menu" "$NAV" | head -n 5
else
  echo "  $NAV not found"
fi

echo -e "\n[4] Sanity: Argon/Bootstrap JS present in rendered /org/dashboard? (unauth fetch)"
curl -sk https://swaeduae.ae/org/dashboard | egrep -m1 -n "bootstrap\.min\.js|argon-dashboard\.min\.js" || echo "  (JS tag not seen in unauth fetch — expected if route redirects.)"

echo -e "\n[5] Quick heuristics:"
awk '
/org-menu-minimal:start/,/org-menu-minimal:end/ {
  if($0 ~ /position:\s*fixed/){ fixed=1 }
  if($0 ~ /max-height:/){ mh=1 }
  if($0 ~ /overflow:\s*auto/){ of=1 }
  if($0 ~ /transform:\s*none/){ tf=1 }
  if($0 ~ /width:\s*min\(92vw, ?(3|4)[0-9]{2}px\)/){ wd=1 }
}
END{
  print "  position:fixed  : " (fixed?"OK":"MISSING")
  print "  max-height      : " (mh?"OK":"MISSING")
  print "  overflow:auto   : " (of?"OK":"MISSING")
  print "  transform:none  : " (tf?"OK":"MISSING")
  print "  reasonable width: " (wd?"OK":"MISSING")
}'
"$LAY"

echo -e "\n[6] Any other dropdown CSS overrides that might conflict?"
grep -RIn --include='*.blade.php' -E 'dropdown-menu[^}]*position:|dropdown-menu[^}]*transform:' resources/views \
  | grep -v "$LAY" || echo "  (no other overrides found)"

echo -e "\n=== END DIAG ==="
