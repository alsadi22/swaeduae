<?php
$p = 'routes/web.php';
$c0 = file_get_contents($p);
$c  = $c0;

/* Fix ->prefix(['...']) to ->prefix('...') */
$c = preg_replace(
    "/->\\s*prefix\\s*\\(\\s*\\[\\s*(['\"])([^'\"\\]]+)\\1\\s*\\]\\s*\\)/s",
    "->prefix('$2')",
    $c
);

/* Fix 'prefix' => ['...'] to 'prefix' => '...' inside group arrays */
$c = preg_replace(
    "/(['\"])prefix\\1\\s*=>\\s*\\[\\s*(['\"])([^'\"\\]]+)\\2\\s*\\]/s",
    "'prefix' => '$3'",
    $c
);

/* (Rare) Fix ->name(['foo.']) to ->name('foo.') */
$c = preg_replace(
    "/->\\s*name\\s*\\(\\s*\\[\\s*(['\"])([^'\"\\]]+)\\1\\s*\\]\\s*\\)/s",
    "->name('$2')",
    $c
);

/* Clean up any duplicated whitespace artifacts */
$c = preg_replace("/[ \\t]+\\)/", ")", $c);

if ($c !== $c0) { file_put_contents($p, $c); echo "normalized prefixes\n"; }
else { echo "no changes\n"; }
