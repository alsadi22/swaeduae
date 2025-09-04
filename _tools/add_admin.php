<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

if ($argc < 3) { fwrite(STDERR, "Usage: php _tools/add_admin.php email password [name]\n"); exit(1); }
[$script, $email, $password, $name] = array_pad($argv, 4, null);
$name = $name ?: 'Administrator';

$userClass = \App\Models\User::class;
$user = $userClass::where('email', $email)->first();

if (!$user) {
  $user = new $userClass();
  if (property_exists($user,'name')) $user->name = $name;
  $user->email = $email;
  $user->password = Hash::make($password);
  if (property_exists($user,'email_verified_at')) $user->email_verified_at = Carbon::now();
  $user->save();
  echo "Created user {$email}\n";
} else {
  $user->password = Hash::make($password);
  $user->save();
  echo "Updated password for {$email}\n";
}

// Resolve configured Role model & table names
$roleModel = config('permission.models.role') ?: \Spatie\Permission\Models\Role::class;
$tables    = (array) config('permission.table_names', []);
$rolesTbl  = $tables['roles']            ?? 'roles';
$modelHas  = $tables['model_has_roles']  ?? 'model_has_roles';

$hasRoles = Schema::hasTable($rolesTbl) && Schema::hasTable($modelHas);
if (!$hasRoles && Schema::hasTable('acl_roles') && Schema::hasTable('acl_model_has_roles')) {
  $rolesTbl = 'acl_roles';
  $modelHas = 'acl_model_has_roles';
  $hasRoles = true;
}
if ($hasRoles) {
  try {
    $guard = config('auth.defaults.guard', 'web');
    // Create/find role directly via DB to respect custom table names
    $roleId = DB::table($rolesTbl)->where(['name'=>'admin','guard_name'=>$guard])->value('id');
    if (!$roleId) {
      $roleId = DB::table($rolesTbl)->insertGetId([
        'name' => 'admin',
        'guard_name' => $guard,
      ]);
      echo "Created role 'admin' in {$rolesTbl}.\n";
    }
    // Assign pivot in model_has_roles
    $modelType = get_class($user);
    $morphKey = config('permission.column_names.model_morph_key','model_id');
    $existing = DB::table($modelHas)
      ->where(['role_id'=>$roleId,'model_type'=>$modelType,$morphKey=>$user->getKey()])
      ->exists();
    if (!$existing) {
      DB::table($modelHas)->insert([
        'role_id' => $roleId,
        'model_type' => $modelType,
        $morphKey => $user->getKey(),
      ]);
      echo "Assigned 'admin' role via {$modelHas}.\n";
    } else {
      echo "User already has 'admin' role.\n";
    }
  } catch (\Throwable $e) {
    echo "Warning: role assignment failed: ".$e->getMessage()."\n";
  }
} else {
  echo "Spatie role tables not present ({$rolesTbl}/{$modelHas}); skipped role assignment.\n";
}
