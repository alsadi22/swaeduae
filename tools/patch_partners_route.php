<?php
$f='routes/web.php';
$c=file_get_contents($f);
if($c===false){fwrite(STDERR,"cannot read routes/web.php\n");exit(1);}
$c=preg_replace("#Route::.*?get\\(\\s*['\\\"]/partners['\\\"][\\s\\S]*?;\\s*#s","",$c,-1,$n);
$c=rtrim($c)."\n\nRoute::get('/partners', function(){ return view('public.partners'); })->name('partners.index');\n";
file_put_contents($f,$c);
echo "Removed $n old /partners route(s); appended canonical.\n";
