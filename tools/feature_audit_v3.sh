#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -Eeuo pipefail
cd /var/www/swaeduae

say(){ printf '\n=== %s ===\n' "$*"; }

say "App env / versions"
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo 'env='.config('app.env').' php='.PHP_VERSION.' laravel='.app()->version().' APP_DEBUG='.(config('app.debug')?'true':'false').PHP_EOL;"

say "Users schema (profile/consent fields)"
php -r "
require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Schema;
\$cols=['mobile','phone','email_verified_at','terms_accepted_at','mobile_verified_at'];
foreach(\$cols as \$c){ printf('%-20s %s'.PHP_EOL, \$c, Schema::hasColumn('users',\$c)?'YES':'no'); }"

say "DB tables & key columns (volunteer-critical + specials)"
php -r "
require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Schema;
function has(\$t){return Schema::hasTable(\$t)?'YES':'no';}
function cols(\$t){return Schema::hasTable(\$t)?implode(',',Schema::getColumnListing(\$t)):'-';}
\$check=['opportunities','applications','attendances','hours','certificates','volunteer_profiles','org_profiles','qr_scans','qr_tokens','certificate_templates','certificate_requests','events','shifts'];
foreach(\$check as \$t){ printf('%-22s %-3s | %s'.PHP_EOL, \$t, has(\$t), cols(\$t)); }"

say "Routes present (browse/apply, profile, QR, hours, certificates)"
php -r "
require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Route;
\$need=['my/profile','opportunities','org/opportunities','applications','attendance','checkin','check-in','kiosk','qr/verify','qr/checkin','qr/checkout','certificates','certificate'];
foreach(\$need as \$p){ \$hit=false; foreach(Route::getRoutes() as \$r){ if(stripos(\$r->uri(),\$p)!==false){ \$hit=true; break; } }
  printf('%-15s %s'.PHP_EOL,\$p,\$hit?'YES':'no');
}
"

say "Config snapshot (hours / geofence / transcript threshold)"
php -r "
require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo 'hours.php present: '.(file_exists(config_path('hours.php'))?'YES':'no').PHP_EOL;
\$h = (array)config('hours');
foreach(['auto','geofence_meters','clip_to_shift','round_to_min','min_eligible_min','token_ttl_seconds','enable_kiosk','transcript_min_hours'] as \$k){
  \$v = array_key_exists(\$k,\$h)?(is_bool(\$h[\$k])?(\$h[\$k]?'true':'false'):\$h[\$k]):'(missing)';
  echo \"hours.\$k = \$v\".PHP_EOL;
}
"

say "Controllers present?"
php -r "
require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
\$classes=[
 'App\Http\Controllers\My\ProfileController',
 'App\Http\Controllers\Org\OpportunityController',
 'App\Http\Controllers\Org\ApplicantsController',
 'App\Http\Controllers\CertificateController',
 'App\Http\Controllers\CertificatesController',
 'App\Http\Controllers\QR\VerifyController',
 'App\Http\Controllers\QR\CheckinController',
];
foreach(\$classes as \$c){ printf('%-55s %s'.PHP_EOL,\$c, class_exists(\$c)?'OK':'missing'); }
"

say "Views / templates"
for f in \
  resources/views/my/profile.blade.php \
  resources/views/qr/verify.blade.php \
  resources/views/certificates/index.blade.php \
  resources/views/certificates \
; do
  if [ -e "$f" ]; then echo "OK $f"; else echo "MISS $f"; fi
done

say "Queue & mail"
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo 'queue='.config('queue.default').PHP_EOL; echo 'mail='.config('mail.mailer').PHP_EOL;"

say "HTTP probes (expect 200 except /my/profile as 302 if guest)"
for u in / /opportunities /applications /qr/verify /certificates /my/profile; do
  code=$(curl -s -o /dev/null -w "%{http_code}" "https://swaeduae.ae$u"); printf ' %s -> %s\n' "$u" "$code";
done

say "Feature matrix (summary)"
php -r "
require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Schema; use Illuminate\Support\Facades\Route;
function T(\$t){return Schema::hasTable(\$t);}
function R(\$needle){ foreach(Route::getRoutes() as \$r){ if(stripos(\$r->uri(),\$needle)!==false) return true; } return false; }
\$autoHours = T('attendances') && T('hours');
\$geoConfig = is_array(config('hours')) && !is_null(config('hours.geofence_meters'));
\$clipShift = (bool)config('hours.clip_to_shift', false);
\$qrVerifyR = R('qr/verify');
\$qrCheckin = R('qr/checkin') || R('checkin');
\$qrCheckout= R('qr/checkout') || R('checkout');
\$certTbl   = T('certificates');
\$certPage  = R('certificates');
\$appsFlow  = T('applications') && (R('opportun')||R('opportunities'));
\$templates = T('certificate_templates');
\$requests  = T('certificate_requests');
printf(\"Auto-hour storage    : %s\\n\", \$autoHours?'YES':'no');
printf(\"Geofence config      : %s (meters=%s)\\n\", \$geoConfig?'YES':'no', (string)config('hours.geofence_meters'));
printf(\"Clip-to-shift        : %s\\n\", \$clipShift?'ON':'OFF');
printf(\"QR verify route      : %s\\n\", \$qrVerifyR?'YES':'no');
printf(\"QR check-in/out API  : %s / %s\\n\", \$qrCheckin?'YES':'no', \$qrCheckout?'YES':'no');
printf(\"Certificates table   : %s\\n\", \$certTbl?'YES':'no');
printf(\"Certificates page    : %s\\n\", \$certPage?'YES':'no');
printf(\"Applications flow    : %s\\n\", \$appsFlow?'YES':'no');
printf(\"Templates/Requests  : %s / %s\\n\", \$templates?'YES':'no', \$requests?'YES':'no');
"
