<?php
$root = realpath(__DIR__.'/..'); $root = dirname($root);
$targets = ['app/Models/Opportunity.php','app/Models/Event.php'];
$model = null;
foreach ($targets as $t) {
  if (is_file($root.'/'.$t)) { $model = $root.'/'.$t; break; }
}
if (!$model) { fwrite(STDERR, "No model found (Opportunity.php/Event.php)\n"); exit(0); }

$src = file_get_contents($model);
$orig = $src;

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
      "        return \\$q; // no-op if neither column exists\n".
      "    }\n".
      "}\n", $src, 1);
}

if ($src !== $orig) {
  copy($model, $model.'.bak_'.date('YmdHis'));
  file_put_contents($model, $src);
  echo "Patched: $model\n";
} else {
  echo "No changes needed: $model\n";
}
