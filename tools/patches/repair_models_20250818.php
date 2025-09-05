<?php
/**
 * Permanent repair for app/Models/Event.php and Opportunity.php:
 * - Move "use Illuminate\Support\Facades\Schema;" to AFTER the namespace.
 * - Ensure volunteers() closes properly in Event model.
 * - Ensure scopePublished() exists (clean, valid) in both models.
 * - Ensure class has a final closing brace.
 * Backup each file as .fixbak_YYYYmmddHHMMSS before changes.
 */

$root = getcwd();
$models = ["app/Models/Event.php","app/Models/Opportunity.php"];

function backup_write($file, $new) {
  if (!file_exists($file)) return;
  $old = file_get_contents($file);
  if ($old === $new) { echo "No change: $file\n"; return; }
  copy($file, $file.'.fixbak_'.date('YmdHis'));
  file_put_contents($file, $new);
  echo "Repaired: $file\n";
}

function ensure_schema_after_namespace($src) {
  // normalize newlines
  $s = str_replace(["\r\n","\r"], "\n", $src);
  // remove all Schema imports
  $s = preg_replace('/^use\s+Illuminate\\\\Support\\\\Facades\\\\Schema;\s*$/mi', '', $s);
  // find namespace line
  if (preg_match('/^namespace\s+App\\\\Models;\s*$/mi', $s, $m, PREG_OFFSET_CAPTURE)) {
    $pos = $m[0][1] + strlen($m[0][0]);
    // check if there is already a Schema import after namespace (now removed all)
    $s = substr($s,0,$pos) . "\nuse Illuminate\\Support\\Facades\\Schema;\n" . substr($s,$pos);
  } else {
    // if namespace missing (shouldn’t happen), just ensure import near top
    $s = preg_replace('/^<\?php\s*/', "<?php\nnamespace App\\Models;\nuse Illuminate\\Support\\Facades\\Schema;\n", $s, 1);
  }
  // remove any duplicate blank lines
  $s = preg_replace("/\n{3,}/", "\n\n", $s);
  return $s;
}

function ensure_scope_published($src) {
  if (strpos($src, 'function scopePublished(') !== false) return $src;
  // inject just before final class closing brace
  $pos = strrpos($src, "}");
  if ($pos === false) { $src .= "\n"; $pos = strlen($src); }
  $scope =
    "\n    /** Only published rows if columns exist */\n".
    "    public function scopePublished(\$q){\n".
    "        \$t = \$q->getModel()->getTable();\n".
    "        if (Schema::hasColumn(\$t,'is_published')) return \$q->where('is_published',1);\n".
    "        if (Schema::hasColumn(\$t,'published_at')) return \$q->whereNotNull('published_at');\n".
    "        return \$q;\n".
    "    }\n";
  return substr($src,0,$pos) . rtrim($scope,"\n") . "\n}\n";
}

function ensure_final_closing_brace($src) {
  // simple brace count; if one more "{" than "}", add a "}" at end
  $o = substr_count($src, "{");
  $c = substr_count($src, "}");
  if ($o > $c) $src .= str_repeat("}\n", $o - $c);
  return $src;
}

foreach ($models as $rel) {
  $file = $root . "/" . $rel;
  if (!is_file($file)) { echo "Skip (not found): $rel\n"; continue; }
  $s = file_get_contents($file);
  $o = $s;

  // 1) Namespace/import ordering
  $s = ensure_schema_after_namespace($s);

  // 2) Event-specific: ensure volunteers() has a closing "}" on same line if missing
  if (basename($file) === "Event.php") {
    // If volunteers() line contains withTimestamps(); but no "}" before next newline, append " }"
    $s = preg_replace(
      '/(public\s+function\s+volunteers\s*\(\)\s*\{\s*return\s+\$this->belongsToMany\([^;]+?withTimestamps\(\);\s*)(?=\n)/',
      '$1 }',
      $s, 1
    );
  }

  // 3) Ensure scopePublished() exists (clean)
  $s = ensure_scope_published($s);

  // 4) Ensure final class brace exists
  $s = ensure_final_closing_brace($s);

  // 5) Trim duplicate blank lines around headers
  $s = preg_replace("/\n{3,}/", "\n\n", $s);

  backup_write($file, $s);
}
