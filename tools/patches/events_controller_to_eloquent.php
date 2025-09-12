<?php
/**
 * Idempotent repair for app/Http/Controllers/EventController.php:
 * - Ensure namespace/uses (including App\Models\Event)
 * - index(): Eloquent ->published()->with([...])->paginate(12) + filters + ordering
 * - show(): Eloquent with slug-or-id lookup + published() guard
 * - keep/add sharjah()
 * Backs up the file if changed.
 */
$root = getcwd();
$file = $root.'/app/Http/Controllers/EventController.php';
if (!is_file($file)) { fwrite(STDERR,"Missing EventController.php\n"); exit(1); }
$s = file_get_contents($file);
$o = $s;
$changed = false;

# Ensure namespace
if (strpos($s, "namespace App\\Http\\Controllers;") === false) {
  $s = preg_replace('/^<\?php\s*/', "<?php\nnamespace App\\Http\\Controllers;\n", $s, 1);
  $changed = true;
}

# Ensure uses
$uses = [
  "use Illuminate\\Http\\Request;",
  "use Illuminate\\Support\\Facades\\Schema;",
  "use App\\Models\\Event;",
];
foreach ($uses as $u) {
  if (strpos($s, $u) === false) {
    if (preg_match('/^namespace\s+App\\\\Http\\\\Controllers;\s*$/mi', $s, $m, PREG_OFFSET_CAPTURE)) {
      $insertPos = $m[0][1] + strlen($m[0][0]);
      $s = substr($s,0,$insertPos) . "\n$u\n" . substr($s,$insertPos);
    } else {
      $s = preg_replace('/^<\?php\s*/', "<?php\n$u\n", $s, 1);
    }
    $changed = true;
  }
}

# Ensure class declaration
if (strpos($s, 'class EventController extends Controller') === false) {
  $s = preg_replace('/class\s+EventController[^\{]*\{?/', "class EventController extends Controller\n{", $s, 1);
  $changed = true;
}

# Replace/insert index() with Eloquent published() + eager load
$index = <<<'PHPIDX'
    public function index(Request $request)
    {
        $q        = trim((string)$request->get('q', ''));
        $category = $request->get('category');
        $region   = $request->get('region');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $sort     = $request->get('sort', 'newest');

        $query = Event::query()->published()
            ->with(['location','organization','category']); // eager loading

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->when(Schema::hasColumn('events','title'),       fn($x)=>$x->orWhere('title','LIKE',"%{$q}%"))
                  ->when(Schema::hasColumn('events','description'), fn($x)=>$x->orWhere('description','LIKE',"%{$q}%"));
            });
        }

        if (!empty($category)) {
            if (Schema::hasColumn('events','category'))    { $query->where('category', $category); }
            if (Schema::hasColumn('events','category_id')) { $query->where('category_id', $category); }
        }
        if (!empty($region) && Schema::hasColumn('events','region')) {
            $query->where('region', $region);
        }
        if (!empty($dateFrom)) {
            $col = Schema::hasColumn('events','start_at') ? 'start_at' : (Schema::hasColumn('events','start_date') ? 'start_date' : null);
            if ($col) $query->whereDate($col, '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $col = Schema::hasColumn('events','end_at') ? 'end_at' : (Schema::hasColumn('events','end_date') ? 'end_date' : null);
            if ($col) $query->whereDate($col, '<=', $dateTo);
        }

        if ($sort === 'closing_soon') {
            // emulate COALESCE(...) ASC
            $query->when(Schema::hasColumn('events','deadline'),  fn($q)=>$q->orderBy('deadline','asc'))
                  ->when(Schema::hasColumn('events','end_date'),  fn($q)=>$q->orderBy('end_date','asc'))
                  ->when(Schema::hasColumn('events','start_date'),fn($q)=>$q->orderBy('start_date','asc'))
                  ->orderBy('id','asc');
        } else {
            $query->when(Schema::hasColumn('events','created_at'),
                        fn($q)=>$q->orderBy('created_at','desc'),
                        fn($q)=>$q->orderBy('id','desc'));
        }

        $events = $query->paginate(12)->withQueryString();
        return view('events.index', compact('events','q','category','region','dateFrom','dateTo','sort'));
    }
PHPIDX;

if (preg_match('/public\s+function\s+index\s*\([^)]*\)\s*\{.*?\n\}\s*/s', $s)) {
  $s = preg_replace('/public\s+function\s+index\s*\([^)]*\)\s*\{.*?\n\}\s*/s', $index."\n", $s, 1);
  $changed = true;
} else {
  $pos = strrpos($s, "}");
  if ($pos === false) { $s .= "\n}\n"; $pos = strrpos($s, "}"); }
  $s = substr($s,0,$pos) . "\n\n" . trim($index) . "\n\n}\n";
  $changed = true;
}

# Replace/insert show() with published guard + slug-or-id
$show = <<<'PHPSHOW'
    public function show($idOrSlug)
    {
        $q = Event::query()->published();

        if (Schema::hasColumn('events','slug') && !is_numeric($idOrSlug)) {
            $q->where('slug', $idOrSlug);
        } else {
            $q->where('id', (int)$idOrSlug);
        }

        $event = $q->firstOrFail();
        return view('events.show', compact('event'));
    }
PHPSHOW;

if (preg_match('/public\s+function\s+show\s*\([^)]*\)\s*\{.*?\n\}\s*/s', $s)) {
  $s = preg_replace('/public\s+function\s+show\s*\([^)]*\)\s*\{.*?\n\}\s*/s', $show."\n", $s, 1);
  $changed = true;
} else {
  $pos = strrpos($s, "}");
  if ($pos === false) { $s .= "\n}\n"; $pos = strrpos($s, "}"); }
  $s = substr($s,0,$pos) . "\n\n" . trim($show) . "\n\n}\n";
  $changed = true;
}

# Insert sharjah() if missing
if (strpos($s, "function sharjah(") === false) {
  $shj = <<<'PHPX'
    public function sharjah(Request $request)
    {
        $request->merge(['region' => 'sharjah']);
        return $this->index($request);
    }
PHPX;
  $pos = strrpos($s, "}");
  if ($pos === false) { $s .= "\n}\n"; $pos = strrpos($s, "}"); }
  $s = substr($s,0,$pos) . "\n\n" . trim($shj) . "\n\n}\n";
  $changed = true;
}

# Tidy blanks
$s = preg_replace("/\n{3,}/", "\n\n", $s);

if ($changed && $s !== $o) {
  copy($file, $file.'.eloquent_fix_'.date('YmdHis'));
  file_put_contents($file, $s);
  echo "EventController converted to Eloquent with published() + eager loading.\n";
} else {
  echo "No changes needed.\n";
}
