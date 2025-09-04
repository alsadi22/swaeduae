<?php
$path = 'app/Http/Middleware/MicroCache.php';
if (!file_exists($path)) { echo "Missing $path\n"; exit; }

$code = file_get_contents($path);

// Replace return $response; with safe guard
if (strpos($code, "X-MicroCache") === false || strpos($code, "SKIP") === false) {
    $code = preg_replace(
        '/return\s+\$response\s*;/',
        "if (method_exists(\$response,'headers')) {\n".
        "    try {\n".
        "        if (!\$response->headers->has('X-MicroCache') || trim((string)\$response->headers->get('X-MicroCache'))==='') {\n".
        "            \$response->headers->set('X-MicroCache','SKIP');\n".
        "        }\n".
        "    } catch (\\Throwable \$e) {\n".
        "        \Log::warning('MicroCache header skipped: '.\$e->getMessage());\n".
        "    }\n".
        "}\n".
        "return \$response;",
        $code
    );
    file_put_contents($path,$code);
    echo "Patched $path\n";
} else {
    echo "Already patched $path\n";
}
