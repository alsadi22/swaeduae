<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Hash; use Illuminate\Support\Facades\Config;
$email='admin@swaeduae.ae'; $pass=getenv('ADMIN_PASS')?:'CHANGE_ME_NOW!';
$userClass = Config::get('auth.providers.users.model') ?? App\Models\User::class;
$u = $userClass::where('email',$email)->first();
if(!$u){ $u=new $userClass(); if(property_exists($u,'name'))$u->name='Admin'; $u->email=$email; $u->password=Hash::make($pass); if(property_exists($u,'email_verified_at'))$u->email_verified_at=now(); $u->save(); echo ">> created $email\n"; }
$guard=Config::get('auth.defaults.guard','web');
try{ $role=Spatie\Permission\Models\Role::firstOrCreate(['name'=>'admin','guard_name'=>$guard]); if(method_exists($u,'assignRole')){$u->assignRole($role); echo ">> role admin assigned\n";} }catch(\Throwable $e){ echo "!! role assignment failed: {$e->getMessage()}\n"; }
