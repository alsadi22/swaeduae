<?php
chdir(__DIR__);
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function p($k,$v){ echo $k,'=',(is_bool($v)?($v?'true':'false'):(is_scalar($v)?$v:json_encode($v))),PHP_EOL; }

echo "LARAVEL\n";
echo "version=", \Illuminate\Foundation\Application::VERSION, PHP_EOL;
p('app.env', config('app.env'));
p('app.debug', config('app.debug'));
p('app.url', config('app.url'));

echo "AUTH\n";
$g = config('auth.guards.admin');
p('guard.admin.driver', $g['driver'] ?? '<unset>');
p('guard.admin.provider', $g['provider'] ?? '<unset>');
if (isset($g['provider'])) {
  $prov = config('auth.providers.'.$g['provider']);
  p('provider.driver', $prov['driver'] ?? '<unset>');
  p('provider.model',  $prov['model']  ?? '<unset>');
  $model = $prov['model'] ?? null;
  p('provider.model.exists', $model && class_exists($model));
}

echo "ALIASES\n";
$aliases = app('router')->getMiddleware();
foreach (['auth','web','guest'] as $a) { echo $a,'=>',($aliases[$a]??'<missing>'),PHP_EOL; }

echo "ROUTES\n";
foreach (app('router')->getRoutes() as $r) {
  $n = $r->getName();
  if ($n && strpos($n,'admin.')===0) echo "route:",$n,PHP_EOL;
}
