<?php
$ctl = __DIR__ . '/../app/Http/Controllers/Org/OpportunityController.php';
$mw  = __DIR__ . '/../app/Http/Middleware/EnsureOrg.php';

/** small helper */
function file_replace($path, $from, $to, $limit = -1) {
  $src = file_get_contents($path);
  if ($src === false) { fwrite(STDERR, "Cannot read $path\n"); exit(1); }
  $dst = preg_replace($from, $to, $src, $limit, $count);
  if ($count === 0) { fwrite(STDERR, "No match for pattern in $path\n"); }
  file_put_contents($path, $dst);
}

/* ---- Controller patches ---- */
$src = file_get_contents($ctl);
if ($src === false) { fwrite(STDERR, "Cannot read $ctl\n"); exit(1); }

/* 1) ensure we import Schema */
if (strpos($src, 'use Illuminate\\Support\\Facades\\Schema;') === false) {
  $src = preg_replace(
    '/use\s+Illuminate\\\\Support\\\\Facades\\\\DB;(\s*)/m',
    "use Illuminate\\Support\\Facades\\DB;$1use Illuminate\\Support\\Facades\\Schema;$1",
    $src, 1
  );
}

/* 2) insert new auto method right after class opening brace */
$autoMethod = <<<'CODE'

    /**
     * Get org for current user; auto-create if missing.
     */
    protected function orgFromRequestAuto(\Illuminate\Http\Request $request)
    {
        $u = $request->user();
        if (!$u) return null;

        $match = function($w) use ($u) {
            $w->orWhere('owner_user_id', $u->id)
              ->orWhere('user_id',      $u->id)
              ->orWhere('owner_id',     $u->id);
        };

        $org = DB::table('organizations')->where($match)->first();

        if (!$org) {
            $cols = [
                'name'       => ($u->name ?? 'My Organization'),
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('organizations','owner_user_id')) {
                $cols['owner_user_id'] = $u->id;
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('organizations','user_id')) {
                $cols['user_id'] = $u->id;
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('organizations','owner_id')) {
                $cols['owner_id'] = $u->id;
            }
            DB::table('organizations')->insert($cols);
            $org = DB::table('organizations')->where($match)->first();
        }

        return $org;
    }

CODE;

if (!preg_match('/class\s+OpportunityController[^{]*\{\s*$/m', $src)) {
  // fallback: insert after first class line and opening brace
  $src = preg_replace('/(class\s+OpportunityController[^{]*\{\s*)/m', "$1\n$autoMethod", $src, 1, $countA);
} else {
  $src = preg_replace('/(class\s+OpportunityController[^{]*\{\s*)$/m', "$1\n$autoMethod", $src, 1, $countA);
}

/* 3) replace calls: orgFromRequest(…) -> orgFromRequestAuto(…) */
$src = str_replace('orgFromRequest($request)', 'orgFromRequestAuto($request)', $src);

/* Save controller */
file_put_contents($ctl, $src);
echo "Patched controller auto-org.\n";

/* ---- Middleware patch: auto-create + attach ---- */
$mwSrc = file_get_contents($mw);
if ($mwSrc === false) { fwrite(STDERR, "Cannot read $mw\n"); exit(1); }

/* import Schema if missing */
if (strpos($mwSrc, 'use Illuminate\\Support\\Facades\\Schema;') === false) {
  $mwSrc = preg_replace(
    '/use\s+Illuminate\\\\Support\\\\Facades\\\\DB;(\s*)/m',
    "use Illuminate\\Support\\Facades\\DB;$1use Illuminate\\Support\\Facades\\Schema;$1",
    $mwSrc, 1
  );
}

/* add auto-create block after we fetch $org */
$mwSrc = preg_replace_callback(
  '/(\$org\s*=\s*DB::table\(\'organizations\'\).*?->first\(\);)(\s*if\s*\(\!\\$org\s*&&\s*\!\\$request->is\(\'org\/setup\*\')\)\s*\{.*?\}\s*)/s',
  function($m){
    $extra = <<<'AUTO'

        if (!$org) {
            $cols = [
                'name'       => ($u->name ?? 'My Organization'),
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('organizations','owner_user_id')) {
                $cols['owner_user_id'] = $u->id;
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('organizations','user_id')) {
                $cols['user_id'] = $u->id;
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('organizations','owner_id')) {
                $cols['owner_id'] = $u->id;
            }
            DB::table('organizations')->insert($cols);
            $org = DB::table('organizations')
                ->where(function($w) use ($u) {
                    $w->orWhere('owner_user_id',$u->id)
                      ->orWhere('user_id',$u->id)
                      ->orWhere('owner_id',$u->id);
                })->first();
        }
AUTO;
    return $m[1] . $extra . $m[2];
  },
  $mwSrc,
  1,
  $cntMw
);

/* Save middleware */
file_put_contents($mw, $mwSrc);
echo "Patched middleware auto-org.\n";
