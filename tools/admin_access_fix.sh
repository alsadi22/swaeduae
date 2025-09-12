#!/usr/bin/env bash
set -euo pipefail

EMAIL="${1:-admin@swaeduae.ae}"
NEWPASS="${2:-Admin@2025!Temp}"

echo "== Gate present? =="
php artisan tinker --execute='use Illuminate\Support\Facades\Gate; echo Gate::has("isAdmin")?"isAdmin gate: YES\n":"isAdmin gate: NO\n";'

echo "== User summary =="
EMAIL="$EMAIL" php artisan tinker --execute='
use Illuminate\Support\Facades\DB;
$e=getenv("EMAIL");
$u=DB::table("users")->where("email",$e)->select("id","name","email","role","is_admin","created_at")->first();
echo json_encode($u, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE).PHP_EOL;
'

echo "== Gate decision for user =="
EMAIL="$EMAIL" php artisan tinker --execute='
use App\Models\User; use Illuminate\Support\Facades\Gate;
$e=getenv("EMAIL"); $u=User::where("email",$e)->first();
echo $u ? ("allows isAdmin: ".(Gate::forUser($u)->allows("isAdmin")?"YES":"NO").PHP_EOL) : "user not found\n";
'

echo "== Resetting password & ensuring admin flags =="
EMAIL="$EMAIL" NEWPASS="$NEWPASS" php artisan tinker --execute='
use Illuminate\Support\Facades\Hash; use Illuminate\Support\Facades\DB;
$e=getenv("EMAIL"); $p=getenv("NEWPASS");
DB::table("users")->where("email",$e)->update([
  "password"=>Hash::make($p),
  "is_admin"=>1,
  "role"=>"admin",
  "remember_token"=>null
]);
echo "Updated $e\n";
'
echo "Done."
