#!/usr/bin/env bash
set -Eeuo pipefail
cd /var/www/swaeduae

say(){ printf '\n=== %s ===\n' "$*"; }

say "App env / versions"
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo 'env='.config('app.env').' php='.PHP_VERSION.' laravel='.app()->version().' APP_DEBUG='.(config('app.debug')?'true':'false').PHP_EOL;"

say "DB tables & key columns (volunteer-critical)"
php -r "
require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Schema;
function has(\$t){return Schema::hasTable(\$t)?'YES':'no';}
function cols(\$t){return Schema::hasTable(\$t)?implode(',',Schema::getColumnListing(\$t)):'-';}
\$check=['opportunities','applications','attendances','hours','certificates','volunteer_profiles','org_profiles','qr_scans','qr_tokens'];
foreach(\$check as \$t){ printf('%-20s %-3s | %s'.PHP_EOL, \$t, has(\$t), cols(\$t)); }
"

say "Routes (must exist for features)"
php -r "
require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Route;
\$want=['my/profile','opportunities','org/opportunities','qr/verify','certificates','certificate','check-in','checkin','attendance','applications'];
foreach(\$want as \$p){ \$hit=false; foreach(Route::getRoutes() as \$r){ if(stripos(\$r->uri(),\$p)!==false){ \$hit=true; break; } }
  printf('%-18s %s'.PHP_EOL,\$p,\$hit?'YES':'no');
}
"

say "Config snapshot (hours / geofence)"
php -r "
require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo 'config file present: '.(file_exists(config_path('hours.php'))?'YES':'no').PHP_EOL;
\$cfg = config('hours'); if (is_array(\$cfg)) { foreach(\$cfg as \$k=>\$v){ if(is_array(\$v)) \$v=json_encode(\$v); echo \"hours.\$k = \".(\$v===null?'null':\$v).PHP_EOL; } } else { echo 'hours config not loaded'.PHP_EOL; }
"

say "Controllers present?"
php -r "
require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
\$classes=[
 'App\Http\Controllers\My\ProfileController',
 'App\Http\Controllers\Org\OpportunityController',
 'App\Http\Controllers\Org\App\ApplicantsController',
 'App\Http\Controllers\Org\ApplicantsController',
 'App\Http\Controllers\CertificateController',
 'App\Http\Controllers\CertificatesController',
 'App\Http\Controllers\QR\VerifyController',
 'App\Http\Controllers\QR\CheckinController',
];
foreach(\$classes as \$c){ printf('%-60s %s'.PHP_EOL,\$c, class_exists(\$c)?'OK':'missing'); }
"

say "Views / templates"
for f in \
  resources/views/my/profile.blade.php \
  resources/views/certificates \
  resources/views/qr/verify.blade.php \
  resources/views/opportunities \
; do
  if [ -e "$f" ]; then echo "OK $f"; else echo "MISS $f"; fi
done

say "Queue & mail"
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo 'queue='.config('queue.default').PHP_EOL; echo 'mail='.config('mail.mailer').PHP_EOL;"

say "HTTP probes"
for u in / /opportunities /qr/verify /certificates /my/profile; do
  code=$(curl -s -o /dev/null -w "%{http_code}" "https://swaeduae.ae$u"); printf ' %s -> %s\n' "$u" "$code";
done

say "Feature matrix (summary)"
php -r "
require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Schema; use Illuminate\Support\Facades\Route;
function table(\$t){return Schema::hasTable(\$t);}
function routeHas(\$needle){ foreach(Route::getRoutes() as \$r){ if(stripos(\$r->uri(),\$needle)!==false) return true; } return false; }
\$autoHours = table('hours') || table('attendances');               // storage for counted hours
\$geoConfig = is_array(config('hours'));                             // geofence config present
\$qrVerify = routeHas('qr/verify');                                   // verification endpoint
\$certTbl  = table('certificates');                                   // certificates table exists
\$apply    = table('applications') && routeHas('opportun');           // can apply/browse
printf(\"Auto-hour storage: %s\n\", \$autoHours?'YES':'no');
printf(\"Geofence config  : %s\n\", \$geoConfig?'YES':'no');
printf(\"QR verify route  : %s\n\", \$qrVerify?'YES':'no');
printf(\"Certificates tbl : %s\n\", \$certTbl?'YES':'no');
printf(\"Browse/apply ops : %s\n\", \$apply?'YES':'no');
"
echo
echo "Note: 'YES' here = feature hookup detected; 'no' means we should wire or re-surface it."
