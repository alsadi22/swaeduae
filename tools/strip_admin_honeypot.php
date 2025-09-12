<?php
$f = __DIR__.'/../app/Http/Controllers/Admin/Auth/AdminLoginController.php';
$c = file_get_contents($f);
if ($c === false) { fwrite(STDERR,"Cannot read $f\n"); exit(1); }

$pattern = '/\$this->middleware\(\s*\[(?:\\\\?App\\\\Http\\\\Middleware\\\\Honeypot::class\s*,\s*)?[\'"]throttle:login[\'"]\s*\]\s*\)->only\(\s*[\'"]login[\'"]\s*\);/';
$repl    = "\$this->middleware('throttle:login')->only('login');";

$cnt = 0;
$c = preg_replace($pattern, $repl, $c, 1, $cnt);

if ($cnt) {
    file_put_contents($f,$c);
    echo "Removed Honeypot from AdminLoginController constructor.\n";
} else {
    echo "No matching Honeypot+throttle line found. Showing first 80 lines so you can see what to edit manually:\n";
    $lines = explode("\n",$c);
    for($i=0;$i<min(80,count($lines));$i++){ echo str_pad($i+1,3,' ',STR_PAD_LEFT).": ".$lines[$i]."\n"; }
}
