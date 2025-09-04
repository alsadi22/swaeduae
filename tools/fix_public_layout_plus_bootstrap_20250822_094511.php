<?php
$f='resources/views/layouts/app.blade.php';
$c=@file_get_contents($f); if($c===false){fwrite(STDERR,"Missing $f\n"); exit(1);}
$orig=$c;

/* Remove Argon/Nucleo CSS+JS from public layout */
$c=preg_replace('/^[ \t]*<link[^>]*(argon|nucleo)[^>]*>\R?/mi','',$c);
$c=preg_replace('/^[ \t]*<script[^>]*(argon|perfect-scrollbar)[^>]*>\s*<\/script>\R?/mi','',$c);

/* Ensure Bootstrap CSS is linked (local) before brand.css */
if(strpos($c, "css/bootstrap.min.css")===false){
  $c=preg_replace(
    '/(<meta name="viewport"[^>]*>\s*\R)/i',
    "$1    <link rel=\"stylesheet\" href=\"{{ asset('css/bootstrap.min.css') }}\">\n",
    $c, 1
  );
}

/* De-nest inner <main> -> <section> (single main) */
$pat='/<main id="main"[^>]*>\s*@yield\((["\'])content\1\)\s*<\/main>/m';
if(preg_match($pat,$c)){
  $c=preg_replace($pat,"<section id=\"main\">@yield('content')</section>",$c,1);
}

/* Keep exactly one public footer (components.footer) */
$c=preg_replace('/^[ \t]*@include(?:If)?\((["\'])argon_front[\/_]+_footer\1\)\R?/mi','',$c);
$c=preg_replace('/^[ \t]*@include(?:If)?\((["\'])partials\.footer(?:-nav)?\1\)\R?/mi','',$c);
if(strpos($c,"@include('components.footer')")===false && strpos($c,"@include(\"components.footer\")")===false){
  $c=preg_replace('/<\/body>/i',"  @include('components.footer')\n</body>",$c,1);
}

/* small tidy */
$c=preg_replace('/class="container-fluid py-4 mt-4 mt-4"/','class="container-fluid py-4 mt-4"',$c,1);

if($c!==$orig){ @copy($f,$f.'.bak_'.date('Ymd_His')); file_put_contents($f,$c); echo "Patched $f\n"; }
else{ echo "No changes needed for $f\n"; }
