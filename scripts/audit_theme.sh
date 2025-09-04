#!/usr/bin/env bash
set -euo pipefail
echo "== Masters present =="; ls -la resources/views/layouts/ || true; ls -la resources/views/admin/ || true
echo; echo "== Masters include check =="; grep -nE "bootstrap|bootstrap\.rtl|tailwind|font-awesome" resources/views/layouts/app.blade.php resources/views/admin/layout.blade.php || true
echo; echo "== Extends counts =="; 
echo -n "layouts.app: "; grep -Rno "@extends('layouts.app')" resources/views | wc -l
echo -n 'admin.layout: '; grep -Rno "@extends('admin.layout')" resources/views | wc -l
echo; echo "== Non-standard extends =="; 
grep -RnoE "@extends\((\"|')(layouts\.master|layouts\.app2|layouts\.admin|admin\.master|layouts\/app|admin\/layout2)(\"|')\)" resources/views || true
echo; echo "== Per-target summary =="; 
grep -RnoE "@extends\(['\"][^)]+['\"]\)" resources/views | sed -E "s/.*@extends\(['\"]([^'\"]+)['\"]\).*/\1/" | sort | uniq -c | sort -nr
echo; echo "== Categories link check =="; 
if [ -f resources/views/categories/index.blade.php ]; then
  grep -n "public.opportunities" resources/views/categories/index.blade.php || echo "OK: no hard dependency found."
fi
