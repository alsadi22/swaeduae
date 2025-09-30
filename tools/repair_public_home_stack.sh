#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail

STAMP="$(date +%F_%H%M%S)"

# 1) Ensure minimal SEO component exists so public/layout can render
SEO="resources/views/components/seo.blade.php"
mkdir -p "$(dirname "$SEO")"
if [ -f "$SEO" ]; then cp -a "$SEO" "$SEO.bak_$STAMP"; fi
cat > "$SEO" <<'BLADE'
<title>@yield('title', config('app.name','SwaedUAE'))</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="index,follow">
<link rel="canonical" href="{{ url()->current() }}">
<link rel="icon" href="{{ asset('favicon.ico') }}">
BLADE

# 2) Make the public home view tolerant and point the search to a real route
VIEW="resources/views/public/home.blade.php"
cp -a "$VIEW" "$VIEW.bak_$STAMP"
# use events.browse in the search form
sed -i "s@route('opportunities.index')@route('events.browse')@g" "$VIEW"
# make the loop resilient: tiles ?? opps ?? empty
sed -i -E "s/@forelse\(\s*\$opps\s+as\s+\$o\)/@forelse((\$tiles ?? \$opps ?? collect()) as \$o)/" "$VIEW"

# 3) Rebuild HomeController@index with a minimal, known-good method
CTRL="app/Http/Controllers/HomeController.php"
cp -a "$CTRL" "$CTRL.bak_$STAMP"
cat > "$CTRL" <<'PHP'
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        // Latest events (guard table existence)
        $events = Schema::hasTable('events')
            ? DB::table('events')
                ->select('id','title','city','location','date','start_time','end_time','region','category')
                ->orderByDesc('date')->orderByDesc('id')
                ->limit(20)->get()
                ->map(function ($e) {
                    $e->type  = 'event';
                    $e->slots = null;
                    $e->mode  = stripos(($e->city.' '.$e->location), 'virtual') !== false ? 'virtual' : 'onsite';
                    return $e;
                })
            : collect();

        // Latest opportunities (guard table existence)
        $opps = Schema::hasTable('opportunities')
            ? DB::table('opportunities')
                ->select('id','title','city','location','date','start_time','end_time','region','category','slots','status')
                ->where(function ($w) {
                    $w->where('status', 'open')
                      ->orWhere('status', 'published')
                      ->orWhereNull('status');
                })
                ->orderByDesc('date')->orderByDesc('id')
                ->limit(20)->get()
                ->map(function ($o) {
                    $o->type = 'opportunity';
                    $o->mode = stripos(($o->city.' '.$o->location), 'virtual') !== false ? 'virtual' : 'onsite';
                    return $o;
                })
            : collect();

        $tiles = collect($events)->merge($opps);

        return view('public.home', [
            'cover' => null,
            'tiles' => $tiles,
            'opps'  => $opps,
        ]);
    }
}
PHP

# 4) Recompile blades
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "✅ Repaired public homepage stack."
echo "Backups:"
echo "  - $SEO.bak_$STAMP (if previously existed)"
echo "  - $VIEW.bak_$STAMP"
echo "  - $CTRL.bak_$STAMP"
