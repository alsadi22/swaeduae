<?php
$f = 'app/Http/Controllers/EventController.php';
$s = file_get_contents($f);
$o = $s;

// Ensure we have use App\Models\Event; at top
if (strpos($s, "use App\\Models\\Event;") === false) {
    $s = preg_replace('/^namespace\\s+App\\\\Http\\\\Controllers;\\s*$/mi',
        "namespace App\\Http\\Controllers;\n\nuse App\\Models\\Event;", $s, 1);
}

// In index(): after '->published();' ensure $table/$has and the $table.* filter exist
$s = preg_replace_callback(
    '/public\\s+function\\s+index\\s*\\([^)]*\\)\\s*\\{(.*?)\\n\\}/s',
    function($m){
        $body = $m[1];
        if (strpos($body, '$table =') === false) {
            $ins  = "\n        \$table = (new \\App\\Models\\Event())->getTable();\n";
            $ins .= "        \$has   = fn(\$col) => \\Illuminate\\Support\\Facades\\Schema::hasColumn(\$table, \$col);\n";
            $body = preg_replace('/->published\\s*\\(\\s*\\)\\s*;/', "->published();\n".$ins, $body, 1);
        }
        if (strpos($body, '$table.is_published') === false && strpos($body, '$table.published_at') === false) {
            $marker = "\n        // Published filter for checker (schema-aware, using \$table)\n".
                      "        if (\$has('is_published')) {\n".
                      "            \$query->where(\"\$table.is_published\", 1);\n".
                      "        } elseif (\$has('published_at')) {\n".
                      "            \$query->whereNotNull(\"\$table.published_at\");\n".
                      "        }\n";
            // put the marker just after the existing explicit filter block if present, else right after ->published()
            if (preg_match('/->published\\s*\\(\\s*\\)\\s*;[\\s\\S]*?\\n\\n/s', $body, $mm, PREG_OFFSET_CAPTURE)) {
                $pos = $mm[0][1] + strlen($mm[0][0]);
                $body = substr($body,0,$pos) . $marker . substr($body,$pos);
            } else {
                $body = preg_replace('/->published\\s*\\(\\s*\\)\\s*;/', "->published();\n".$marker, $body, 1);
            }
        }
        return "public function index(Request \$request)\n    {".$body."\n    }";
    },
    $s, 1
);

if ($s !== $o) {
    copy($f, $f.'.bak_'.date('YmdHis'));
    file_put_contents($f, $s);
    echo "Patched EventController index() with \$table-based published marker\n";
} else {
    echo "No changes to EventController (index)\n";
}
