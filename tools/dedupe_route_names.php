<?php
$f = 'routes/web.php';
$c = file_get_contents($f);
if ($c === false) { fwrite(STDERR,"cannot read $f\n"); exit(1); }

$names = ['admin.users.update','admin.opportunities.update'];

foreach ($names as $n) {
    $count = 0;
    $c = preg_replace_callback(
        "/->name\\('".preg_quote($n,"/")."'\\);/",
        function($m) use (&$count) { $count++; return $count === 1 ? $m[0] : ''; },
        $c
    );
}

file_put_contents($f, $c);
echo "Deduped route names.\n";
