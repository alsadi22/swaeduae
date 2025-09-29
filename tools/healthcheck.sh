#!/bin/bash
set -euo pipefail

APP_DIR="/home3/vminingc/swaeduae.ae/laravel-app"
PHP="/opt/alt/php84/usr/bin/php"
URL="https://swaeduae.ae/healthz"
ALERT_LOG="$APP_DIR/storage/logs/alerts.log"
EMAIL_TO="$(grep -E '^ALERT_EMAIL=' "$APP_DIR/.env" | cut -d= -f2-)"

# Call /healthz and evaluate JSON using PHP (no jq dependency)
RAW=$($PHP -r '
  $u=$argv[1];
  $ctx=stream_context_create(["http"=>["timeout"=>10,"ignore_errors"=>true]]);
  $b=@file_get_contents($u,false,$ctx);
  if($b===false){echo json_encode(["ok"=>false,"err"=>"fetch_failed"]); exit;}
  echo $b;
' "$URL" )

# Parse and decide status
STATUS=$($PHP -r '
  $j=json_decode($argv[1], true);
  if(!$j || !isset($j["ok"])) { echo "BAD JSON"; exit(3); }
  $failed = isset($j["queue_failed"]) ? (int)$j["queue_failed"] : 0;
  $ok = $j["ok"] && $failed===0;
  echo $ok ? "OK" : "FAIL";
' "$RAW" ) || STATUS="FAIL"

TS=$(date -Is)
if [ "$STATUS" = "OK" ]; then
  echo "$TS healthcheck OK $RAW"
  exit 0
else
  echo "$TS healthcheck FAIL $RAW" | tee -a "$ALERT_LOG"
  # Try to email if an address and sendmail exist
  if [ -n "$EMAIL_TO" ] && [ -x /usr/sbin/sendmail ]; then
    {
      echo "Subject: [Swaed] Healthcheck FAIL"
      echo "To: $EMAIL_TO"
      echo "From: swaed-monitor@$(hostname -f)"
      echo
      echo "Healthcheck failed at: $TS"
      echo "URL: $URL"
      echo "Payload:"
      echo "$RAW"
    } | /usr/sbin/sendmail -t || true
  fi
  exit 1
fi
