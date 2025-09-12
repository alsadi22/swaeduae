<?php
/**
 * Rebuild/repair the public EventController without overwriting intent:
 * - Ensure class exists with index() + show() (and keep/add sharjah()).
 * - Use Query Builder with a schema-aware published filter.
 * - Keep pagination + explicit ordering.
 * - Only inject missing pieces; keep any existing custom methods.
 * Backs up the file if it changes.
 */

$root = getcwd();
$file = $root.'/app/Http/Controllers/EventController.php';
$changed = false;

$header = <<<PHPH
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
PHPH;

$index = <<<'PHPIDX'
    public function index(Request $request)
    {
        $q        = trim((string)$request->get('q', ''));
        $category = $request->get('category');
        $region   = $request->get('region');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $sort     = $request->get('sort', 'newest');

        $table = 'events';
        $has   = fn($col) => Schema::hasColumn($table, $col);

        $builder = DB::table($table)->select("$table.*");

        // Published filter (schema-aware)
        if ($has('is_published')) {
            $builder->where("$table.is_published", 1);
        } elseif ($has('published_at')) {
            $builder->whereNotNull("$table.published_at");
        }

        if ($q !== '') {
            $builder->where(function ($w) use ($q, $table, $has) {
                if ($has('title'))       { $w->orWhere("$table.title", 'LIKE', "%{$q}%"); }
                if ($has('description')) { $w->orWhere("$table.description", 'LIKE', "%{$q}%"); }
            });
        }

        if (!empty($category) && ($has('category') || $has('category_id'))) {
            $col = $has('category') ? 'category' : 'category_id';
            $builder->where("$table.$col", $category);
        }
        if (!empty($region) && $has('region')) {
            $builder->where("$table.region", $region);
        }
        if (!empty($dateFrom) && ($has('start_date') || $has('start_at'))) {
            $col = $has('start_at') ? 'start_at' : 'start_date';
            $builder->whereDate("$table.$col", '>=', $dateFrom);
        }
        if (!empty($dateTo) && ($has('end_date') || $has('end_at'))) {
            $col = $has('end_at') ? 'end_at' : 'end_date';
            $builder->whereDate("$table.$col", '<=', $dateTo);
        }

        // Ordering
        $closingExpr = DB::raw(
            "COALESCE($table.deadline, $table.end_date, $table.start_date, $table.created_at)"
        );
        if ($sort === 'closing_soon') {
            $builder->orderBy($closingExpr, 'asc');
        } else {
            $orderCol = $has('created_at') ? 'created_at' : 'id';
            $builder->orderBy("$table.$orderCol", 'desc');
        }

        $events = $builder->paginate(12)->withQueryString();

        return view('events.index', compact('events','q','category','region','dateFrom','dateTo','sort'));
    }
PHPIDX;

$show = <<<'PHPSHOW'
    public function show($idOrSlug)
    {
        $table = 'events';
        $has   = fn($col) => Schema::hasColumn($table, $col);

        $q = DB::table($table)->select("$table.*");

        // Published filter on show as well
        if ($has('is_published')) {
            $q->where("$table.is_published", 1);
        } elseif ($has('published_at')) {
            $q->whereNotNull("$table.published_at");
        }

        if ($has('slug') && !is_numeric($idOrSlug)) {
            $q->where("$table.slug", $idOrSlug);
        } else {
            $q->where("$table.id", (int)$idOrSlug);
        }

        $event = $q->first();
        abort_if(!$event, 404);

        return view('events.show', compact('event'));
    }
PHPSHOW;

$sharjah = <<<'PHPSHJ'
    // Optional helper used by routes: /region/sharjah
    public function sharjah(Request $request)
    {
        // Reuse index() but pin the region param if the column exists
        $request->merge(['region' => 'sharjah']);
        return $this->index($request);
    }
PHPSHJ;

function ensure_block(&$s, $methodName, $block) {
    if (strpos($s, "function $methodName(") === false) {
        // Insert before last closing brace of the class
        $pos = strrpos($s, "}");
        if ($pos === false) { $s .= "\n}\n"; $pos = strrpos($s, "}"); }
        $s = substr($s, 0, $pos) . "\n\n" . trim($block) . "\n\n}\n";
        return true;
    }
    return false;
}

// If file missing or clearly not an EventController, (re)create a sane base
if (!is_file($file) || strpos(@file_get_contents($file), 'class EventController extends Controller') === false) {
    $s = $header . "\n" . rtrim($index) . "\n\n" . rtrim($show) . "\n\n" . rtrim($sharjah) . "\n}\n";
    if (is_file($file)) copy($file, $file.'.rebuild_'.date('YmdHis'));
    file_put_contents($file, $s);
    echo "Rebuilt EventController with index(), show(), sharjah().\n";
    exit(0);
}

$s = file_get_contents($file);
$o = $s;

// Ensure namespace and uses exist
if (strpos($s, "namespace App\\Http\\Controllers;") === false) {
    $s = preg_replace('/^<\?php\s*/', "<?php\nnamespace App\\Http\\Controllers;\n", $s, 1, $cntNs);
    if ($cntNs) $changed = true;
}
foreach ([
    "use Illuminate\\Http\\Request;",
    "use Illuminate\\Support\\Facades\\DB;",
    "use Illuminate\\Support\\Facades\\Schema;",
] as $use) {
    if (strpos($s, $use) === false) {
        // Insert after namespace line
        if (preg_match('/^namespace\s+App\\\\Http\\\\Controllers;\s*$/mi', $s, $m, PREG_OFFSET_CAPTURE)) {
            $insertPos = $m[0][1] + strlen($m[0][0]);
            $s = substr($s,0,$insertPos) . "\n$use\n" . substr($s,$insertPos);
            $changed = true;
        } else {
            $s = preg_replace('/^<\?php\s*/', "<?php\n$use\n", $s, 1);
            $changed = true;
        }
    }
}

// Ensure class declaration exists
if (strpos($s, 'class EventController extends Controller') === false) {
    $s = preg_replace('/class\s+EventController[^\{]*\{?/', "class EventController extends Controller\n{", $s, 1, $cntCls);
    if ($cntCls) $changed = true;
}

// Ensure required methods
$changed = ensure_block($s, 'index', $index) || $changed;
$changed = ensure_block($s, 'show',  $show)  || $changed;
// Add sharjah() only if referenced in routes and missing
if (strpos($s, "function sharjah(") === false) {
    $changed = ensure_block($s, 'sharjah', $sharjah) || $changed;
}

// Tidy blank lines
$s = preg_replace("/\n{3,}/", "\n\n", $s);

if ($changed) {
    copy($file, $file.'.fixbak_'.date('YmdHis'));
    file_put_contents($file, $s);
    echo "Patched: EventController (methods/uses/namespace normalized)\n";
} else {
    echo "No changes needed: EventController already OK\n";
}
