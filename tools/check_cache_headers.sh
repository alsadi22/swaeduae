#!/bin/bash
PHP_BIN=${PHP_BIN:-php}
URL="https://swaeduae.ae"

echo "==> Dynamic page (/) [1st request]"
curl -sSI -H "Accept-Language: en" "$URL/" \
  | egrep -i "^(HTTP/|cache-control|content-language|vary|x-microcache|expires)"

echo
echo "==> Dynamic page (/) [2nd request to check microcache]"
curl -sSI -H "Accept-Language: en" "$URL/" \
  | egrep -i "^(HTTP/|cache-control|content-language|vary|x-microcache|expires)"

echo
echo "==> Locale check (/ with en)"
curl -sSI -H "Accept-Language: en" "$URL/" \
  | egrep -i "^(HTTP/|content-language|vary)"

echo
echo "==> Locale check (/ with ar)"
curl -sSI -H "Accept-Language: ar" "$URL/" \
  | egrep -i "^(HTTP/|content-language|vary)"

echo
echo "==> Static asset (/css/app.css)"
curl -sSI "$URL/css/app.css" \
  | egrep -i "^(HTTP/|cache-control|expires|last-modified|etag)"

echo
echo "==> Cacheable page (/about) [1st request]"
curl -sSI -H "Accept-Language: en" "$URL/about" \
  | egrep -i "^(HTTP/|cache-control|content-language|vary|x-microcache)"

echo
echo "==> Cacheable page (/about) [2nd request]"
curl -sSI -H "Accept-Language: en" "$URL/about" \
  | egrep -i "^(HTTP/|cache-control|content-language|vary|x-microcache)"
