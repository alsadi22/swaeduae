<?php
$root = realpath(__DIR__.'/..');            // /laravel-app/tools
$root = dirname($root);                     // /laravel-app
$ctr  = $root.'/app/Http/Controllers/EventController.php';

if (!is_file($ctr)) {
  fwrite(STDERR, "EventController not found at $ctr\n");
  exit(1);
}

$src = file_get_contents($ctr);
$orig = $src;

/* Ensure Schema import */
if (strpos($src, "use Illuminate\\Support\\Facades\\Schema;") === false) {
    $src = preg_replace(
      '/(^use\s+Illuminate\\\\Http\\\\Request;\s*)/m',
      "$0\nuse Illuminate\\Support\\Facades\\Schema;\n",
      $src, 1
    );
}

/* Insert applyPublishedFilter() once */
if (strpos($src, 'protected function applyPublishedFilter(') === false) {
    $src = preg_replace(
      '/class\s+\w+\s+extends\s+Controller\s*\{/',
      "$0\n    /** Apply a safe published filter if the schema supports it */\n".
      "    protected function applyPublishedFilter(\\$builder){\n".
      "        \\$table = \\$builder->getModel()->getTable();\n".
      "        if (\\Illuminate\\Support\\Facades\\Schema::hasColumn(\\$table,'is_published')) {\n".
      "            \\$builder->where('is_published', 1);\n".
      "        } elseif (\\Illuminate\\Support\\Facades\\Schema::hasColumn(\\$table,'published_at')) {\n".
      "            \\$builder->whereNotNull('published_at');\n".
      "        }\n".
      "        return \\$builder;\n".
      "    }\n",
      $src, 1
    );
}

/* Wire into index() (conservative: does not change your ordering/pagination) */
if (!preg_match('/applyPublishedFilter\s*\(/', $src)) {
    $src = preg_replace(
      '/public function index\s*\([^)]*\)\s*\{/',
      "$0\n        \\$query = \\App\\Models\\Event::query();\n        \\$this->applyPublishedFilter(\\$query);",
      $src, 1
    );
    /* Try to convert `$events = Event::...;` to use $query if present */
    $src = preg_replace(
      '/\$events\s*=\s*Event::([^\n;]+);/m',
      "\$events = \$query$1;",
      $src, 1
    );
}

/* Write only if changed */
if ($src !== $orig) {
  copy($ctr, $ctr.'.bak_'.date('YmdHis'));
  file_put_contents($ctr, $src);
  echo "Patched: $ctr\n";
} else {
  echo "No changes needed: $ctr\n";
}
