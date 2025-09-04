#!/usr/bin/env bash
set -euo pipefail
php artisan tinker --execute='
use Illuminate\Support\Facades\DB;

$admins = DB::table("users")
  ->select("id","name","email","role","is_admin","created_at")
  ->where(function($q){
      $q->where("is_admin",1)->orWhere("role","admin");
  })
  ->orderBy("id")
  ->get();

echo "Admin users (is_admin=1 OR role=admin):\n";
echo json_encode($admins, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), PHP_EOL;
'
