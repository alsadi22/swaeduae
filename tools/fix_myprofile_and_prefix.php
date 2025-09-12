<?php
function fixFile($file){
  if (!file_exists($file)) return ['file'=>$file,'changed'=>false,'notes'=>['missing']];
  $c0 = file_get_contents($file); $c = $c0; $notes = [];

  // A) normalize ->prefix(['x']) => ->prefix('x')
  $c2 = preg_replace(
    "/->\\s*prefix\\s*\\(\\s*\\[\\s*(['\"])([^'\"\\]]+)\\1\\s*\\]\\s*\\)/s",
    "->prefix('$2')",
    $c
  ); if ($c2 !== $c){ $notes[]='fixed ->prefix([..])'; $c = $c2; }

  // B) normalize 'prefix' => ['x'] => 'prefix' => 'x' (inside group arrays)
  $c2 = preg_replace(
    "/(['\"])prefix\\1\\s*=>\\s*\\[\\s*(['\"])([^'\"\\]]+)\\2\\s*\\]/s",
    "'prefix' => '$3'",
    $c
  ); if ($c2 !== $c){ $notes[]="fixed 'prefix' => [..]"; $c = $c2; }

  // C) rare: ->name(['foo.']) => ->name('foo.')
  $c2 = preg_replace(
    "/->\\s*name\\s*\\(\\s*\\[\\s*(['\"])([^'\"\\]]+)\\1\\s*\\]\\s*\\)/s",
    "->name('$2')",
    $c
  ); if ($c2 !== $c){ $notes[]='fixed ->name([..])'; $c = $c2; }

  // D) remove any legacy conditional my.profile block
  $c2 = preg_replace(
    "#if\\s*\\(\\s*!\\s*Route::has\\(\\s*['\"]my\\.profile['\"]\\s*\\)\\s*\\)\\s*\\{.*?\\}#s",
    "",
    $c
  ); if ($c2 !== $c){ $notes[]='removed legacy my.profile block'; $c = $c2; }

  // E) remove any one-liner routes registering /my/profile
  $c2 = preg_replace(
    "#Route::[^;]*['\"]/my/profile['\"][^;]*;\\s*#s",
    "",
    $c
  ); if ($c2 !== $c){ $notes[]='removed one-line my.profile routes'; $c = $c2; }

  // F) write back
  if ($c !== $c0){ file_put_contents($file,$c); return ['file'=>$file,'changed'=>true,'notes'=>$notes]; }
  return ['file'=>$file,'changed'=>false,'notes'=>$notes?:['no changes']];
}

$reports = [];
$reports[] = fixFile('routes/web.php');
if (file_exists('routes/z_overrides.php')) $reports[] = fixFile('routes/z_overrides.php');

// G) append canonical protected routes to web.php if missing
$web = 'routes/web.php';
$cw0 = file_get_contents($web); $cw = $cw0;
if (strpos($cw, "->name('my.profile')") === false) {
  $cw .= PHP_EOL."/* === Volunteer Profile (protected) — canonical === */
Route::middleware(['web','auth'])->get(
  '/my/profile',
  [\\App\\Http\\Controllers\\My\\ProfileController::class, 'show']
)->name('my.profile');

Route::middleware(['web','auth'])->post(
  '/my/profile',
  [\\App\\Http\\Controllers\\My\\ProfileController::class, 'update']
)->name('my.profile.update');".PHP_EOL;
}
if ($cw !== $cw0){ file_put_contents($web,$cw); $reports[] = ['file'=>$web,'changed'=>true,'notes'=>['appended canonical my.profile routes']]; }

foreach ($reports as $r){
  echo ($r['changed']?'changed ':'ok ').$r['file']." | ".implode(', ',$r['notes']).PHP_EOL;
}

// final sanity: show remaining bad prefixes if any (for visibility)
$bad = [];
foreach (['routes/web.php','routes/z_overrides.php'] as $chk){
  if (!file_exists($chk)) continue;
  $c = file_get_contents($chk);
  if (preg_match("/->\\s*prefix\\s*\\(\\s*\\[/s",$c)) $bad[] = "$chk: ->prefix([..])";
  if (preg_match("/['\"]prefix['\"]\\s*=>\\s*\\[/s",$c)) $bad[] = "$chk: 'prefix' => [..]";
}
if ($bad){ echo 'remaining bad prefixes:'."\n".implode("\n",$bad)."\n"; }
