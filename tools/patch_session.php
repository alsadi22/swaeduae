<?php
$f='config/session.php';
$c=file_get_contents($f);

// secure => env('SESSION_SECURE_COOKIE', true)
$c=preg_replace(
    "/'secure'\s*=>\s*env\('SESSION_SECURE_COOKIE'[^)]*\)/",
    "'secure' => env('SESSION_SECURE_COOKIE', true)",
    $c,1,$cnt1
);
if(!$cnt1){
    $c=preg_replace("/'secure'\s*=>\s*[^,]+/", "'secure' => env('SESSION_SECURE_COOKIE', true)", $c,1,$cnt1b);
}

// http_only => true
$c=preg_replace("/'http_only'\s*=>\s*[^,]+/","'http_only' => true",$c,1,$cnt2);

// same_site => env('SESSION_SAME_SITE','lax') (add if missing)
if (strpos($c, "'same_site'") !== false) {
    $c=preg_replace("/'same_site'\s*=>\s*[^,]+/","'same_site' => env('SESSION_SAME_SITE','lax')",$c,1,$cnt3);
} else {
    // Insert before the closing array bracket
    $c=preg_replace("/(\];\s*)$/","    'same_site' => env('SESSION_SAME_SITE','lax'),\n$1",$c,1,$cnt3);
}

file_put_contents($f,$c);
