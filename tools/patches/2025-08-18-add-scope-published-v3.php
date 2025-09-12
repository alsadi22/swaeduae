<?php
$root = dirname(realpath(__DIR__.'/..'));
$models = ['app/Models/Event.php','app/Models/Opportunity.php'];
foreach ($models as $rel) {
  $path = $root.'/'.$rel;
  if (!is_file($path)) { echo "Skip: $rel (not found)\n"; continue; }
  $src = file_get_contents($path); $orig = $src;

  if (strpos($src, "use Illuminate\\Support\\Facades\\Schema;") === false) {
      $src = preg_replace('/^<\\?php\\s*/', "<?php\nuse Illuminate\\Support\\Facades\\Schema;\n", $src, 1);
  }

  // Find the final class closing brace and inject scope before it.
  if (strpos($src,'function scopePublished(') === false) {
      // Try to place before last "}\n" cautiously.
      $pos = strrpos($src, "}");
      if ($pos !== false) {
        $scope =
"    /** Query scope: published rows if columns exist */\n".
"    public function scopePublished(\$q){\n".
"        \$t = \$q->getModel()->getTable();\n".
"        if (Schema::hasColumn(\$t,'is_published')) return \$q->where('is_published',1);\n".
"        if (Schema::hasColumn(\$t,'published_at')) return \$q->whereNotNull('published_at');\n".
"        return \$q; // no-op if neither column exists\n".
"    }\n";
        $src = substr($src,0,$pos) . rtrim("\n".$scope, "\n") . "\n}\n";
      }
  }

  if ($src !== $orig) {
    copy($path, $path.'.bak_'.date('YmdHis'));
    file_put_contents($path,$src);
    echo "Patched: $rel\n";
  } else {
    echo "No changes needed: $rel\n";
  }
}
