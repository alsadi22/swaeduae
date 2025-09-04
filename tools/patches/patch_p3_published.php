<?php
$root = realpath(__DIR__.'/..');
$ctr  = $root.'/app/Http/Controllers/EventController.php';
if (!is_file($ctr)) { fwrite(STDERR, "EventController not found at $ctr\n"); exit(1); }
$src = file_get_contents($ctr);

/* Ensure: use Illuminate\Support\Facades\Schema; */
if (strpos($src, "use Illuminate\\Support\\Facades\\Schema;") === false) {
    $src = preg_replace('/^use\s+Illuminate\\\\Http\\\\Request;\s*$/m',
        "use Illuminate\\Http\\Request;\nuse Illuminate\\Support\\Facades\\Schema;", $src, 1);
}

/* Insert helper once */
if (strpos($src, 'applyPublishedFilter(') === false) {
    $src = preg_replace('/class\s+\w+\s+extends\s+Controller\s*\{/',
        "$0\n    /** Apply a safe published filter if the schema supports it */\n".
        "    protected function applyPublishedFilter(\\$builder){\n".
        "        \\$table = \\$builder->getModel()->getTable();\n".
        "        if (\\Illuminate\\Support\\Facades\\Schema::hasColumn(\\$table,'is_published')) { \\$builder->where('is_published',1); }\n".
        "        elseif (\\Illuminate\\Support\\Facades\\Schema::hasColumn(\\$table,'published_at')) { \\$builder->whereNotNull('published_at'); }\n".
        "        return \\$builder;\n".
        "    }\n", $src, 1);
}

/* Wire it into index() without changing your existing ordering/pagination */
if (!preg_match('/applyPublishedFilter\(/', $src)) {
    $src = preg_replace('/public function index\s*\([^)]*\)\s*\{/',
        "$0\n        \\$query = \\App\\Models\\Event::query();\n        \\$this->applyPublishedFilter(\\$query);",
        $src, 1);
}

/* Backup + write */
copy($ctr, $ctr.'.bak_'.date('YmdHis'));
file_put_contents($ctr, $src);
echo "Patched: $ctr\n";
