<?php
$root = realpath(__DIR__.'/..');
$targets = ['app/Models/Opportunity.php','app/Models/Event.php'];
$path = null;
foreach ($targets as $t) { if (is_file($root.'/'.$t)) { $path = $root.'/'.$t; break; } }
if (!$path) { fwrite(STDERR, "No model found (Opportunity.php/Event.php)\n"); exit(0); }
$src = file_get_contents($path);
if (strpos($src,"use Illuminate\\Support\\Facades\\Schema;")===false) {
    $src = preg_replace('/^<\\?php\\s*/', "<?php\nuse Illuminate\\Support\\Facades\\Schema;\n", $src, 1);
}
if (strpos($src,'function scopePublished(')===false) {
    $src = preg_replace('/}\\s*$/',
        "    /** Query scope: published rows if columns exist */\n".
        "    public function scopePublished(\\$q){\n".
        "        \\$t = \\$q->getModel()->getTable();\n".
        "        if (Schema::hasColumn(\\$t,'is_published')) return \\$q->where('is_published',1);\n".
        "        if (Schema::hasColumn(\\$t,'published_at')) return \\$q->whereNotNull('published_at');\n".
        "        return \\$q;\n".
        "    }\n".
        "}\n", $src);
    copy($path, $path.'.bak_'.date('YmdHis'));
    file_put_contents($path, $src);
    echo "Added scopePublished() to $path\n";
} else {
    echo "scopePublished() already present in $path\n";
}
