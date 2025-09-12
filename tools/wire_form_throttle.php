<?php
$f='app/Http/Kernel.php';
$s=file_get_contents($f);
if($s===false){fwrite(STDERR,"[ERR] cannot read $f\n"); exit(1);}

$cls="\\App\\Http\\Middleware\\FormRateLimit::class";

// Ensure $middlewareGroups['web'] exists and inject our class if not present
$s = preg_replace_callback(
    '/\$middlewareGroups\s*=\s*\[(.*?)\];/s',
    function($m) use($cls){
        $block = $m[1];
        // Find the 'web' group array
        $web = preg_replace_callback(
            '/(\'web\'\s*=>\s*\[)(.*?)(\])/s',
            function($w) use($cls){
                $inner = $w[2];
                if (strpos($inner, $cls) !== false) return $w[0]; // already there
                // put our middleware just before SubstituteBindings if possible
                $inner = preg_replace(
                    '/(\s*\\\\Illuminate\\\\Routing\\\\Middleware\\\\SubstituteBindings::class,?)/',
                    "    $cls,\n$1",
                    $inner, 1, $c
                );
                if(!$c){ $inner .= "    $cls,\n"; }
                return $w[1] . $inner . $w[3];
            },
            $block, 1, $did
        );
        if(!$did){ return $m[0]; }
        return '$middlewareGroups = ['.$web.'];';
    },
    $s, 1, $chg
);

if(!$chg){ echo "[WARN] Could not modify web group; please add $cls manually to \$middlewareGroups['web'].\n"; }
copy($f, $f.'.bak_'.date('Ymd_His')); file_put_contents($f, $s);
echo "[OK] FormRateLimit added to web group\n";
