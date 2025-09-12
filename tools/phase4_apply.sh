#!/usr/bin/env bash
set -euo pipefail
APP="/home3/vminingc/swaeduae.ae/laravel-app"
ROOT="/home3/vminingc/swaeduae.ae"
PHP="/usr/bin/php"
TS="$(date +%F-%H%M%S)"
cd "$APP"

echo "== Phase 4 apply =="

# --- SEO partial (hreflang + breadcrumbs JSON-LD) ---
SEO="resources/views/components/seo.blade.php"
mkdir -p "$(dirname "$SEO")"
[ -f "$SEO" ] && cp -a "$SEO" "$SEO.bak.$TS" || true
cat > "$SEO" <<'BLADE'
@php
    $site = config('app.name', 'Swaed UAE');
    $title = trim(($title ?? '') ?: ($pageTitle ?? '') ?: $site);
    $desc  = trim(($description ?? '') ?: 'Volunteer opportunities across the UAE. Join and make an impact.');
    $canonical = ($canonical ?? '') ?: url()->current();
    $image = ($image ?? '') ?: asset('images/og-default.jpg');

    $locales = ['ar','en'];
    $alt = [];
    foreach ($locales as $loc) { $alt[$loc] = request()->fullUrlWithQuery(['lang' => $loc]); }
    $xDefault = url()->current();

    $ld_json = $ld_json ?? null;
    $breadcrumbs = $breadcrumbs ?? null;

    $ld_breadcrumbs = null;
    if (is_array($breadcrumbs) && count($breadcrumbs)) {
        $items = [];
        $pos = 1;
        foreach ($breadcrumbs as $bc) {
            $items[] = ['@type'=>'ListItem','position'=>$pos++,'name'=>(string)($bc['name']??''),'item'=>(string)($bc['url']??'')];
        }
        $ld_breadcrumbs = ['@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>$items];
    }
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $desc }}">
<link rel="canonical" href="{{ $canonical }}" />
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:site_name" content="{{ $site }}">
<meta property="og:image" content="{{ $image }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $desc }}">
<meta name="twitter:image" content="{{ $image }}">
@foreach($alt as $loc => $url)
<link rel="alternate" href="{{ $url }}" hreflang="{{ $loc }}">
@endforeach
<link rel="alternate" href="{{ $xDefault }}" hreflang="x-default">
@if(!empty($ld_json))
<script type="application/ld+json">{!! json_encode($ld_json, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}</script>
@endif
@if(!empty($ld_breadcrumbs))
<script type="application/ld+json">{!! json_encode($ld_breadcrumbs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}</script>
@endif
BLADE
echo "[OK] seo.blade.php written"

# --- Middleware: SetLocale honors ?lang= ---
MID="app/Http/Middleware/SetLocale.php"
mkdir -p "$(dirname "$MID")"
[ -f "$MID" ] && cp -a "$MID" "$MID.bak.$TS" || true
cat > "$MID" <<'PHP'
<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $allowed = ['ar','en'];
        if ($request->filled('lang')) {
            $lang = (string)$request->query('lang');
            if (in_array($lang, $allowed, true)) { session(['locale' => $lang]); }
        }
        $locale = session('locale');
        if (!$locale || !in_array($locale, $allowed, true)) { $locale = 'ar'; }
        app()->setLocale($locale);
        return $next($request);
    }
}
PHP
echo "[OK] SetLocale.php written"

# --- Kernel patcher (alias + web group include) ---
cat > tools/_inject_kernel_setlocale.php <<'PHP'
<?php
$k = "app/Http/Kernel.php";
$s = file_get_contents($k);
if (strpos($s, "'setlocale'") === false) {
  $s = preg_replace("/(protected \\$middlewareAliases\\s*=\\s*\\[)/", "$1\n        'setlocale' => \\\\App\\\\Http\\\\Middleware\\\\SetLocale::class,", $s, 1);
}
if (strpos($s, "\\App\\Http\\Middleware\\SetLocale::class") === false) {
  $s = preg_replace("/('web'\\s*=>\\s*\\[)(.*?)(\\n\\s*\\],)/s", "$1$2\n            \\\\App\\\\Http\\\\Middleware\\\\SetLocale::class,$3", $s, 1);
}
file_put_contents($k, $s);
echo "[OK] Kernel patched for SetLocale\n";
PHP
cp -a app/Http/Kernel.php "app/Http/Kernel.php.bak.$TS"
$PHP tools/_inject_kernel_setlocale.php

# --- Views with breadcrumbs (overwrite cleanly) ---
mkdir -p resources/views/public/opportunities
IDX="resources/views/public/opportunities/index.blade.php"
cp -a "$IDX" "$IDX.bak.$TS" 2>/dev/null || true
cat > "$IDX" <<'BLADE'
@extends('public.layout')
@php
  $title = 'Browse Volunteer Opportunities';
  $description = 'Search and filter volunteer opportunities across the UAE by emirate, city, and category.';
  $breadcrumbs = [
    ['name'=>'Home','url'=>url('/')],
    ['name'=>'Opportunities','url'=>route('opportunities.index')],
  ];
@endphp
@section('content')
  <h1>{{ $title }}</h1>
  <form class="filters" method="get">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Keyword" />
    @if($emirates->count()) 
      <select name="emirate">
        <option value="">All Emirates</option>
        @foreach($emirates as $em)
          <option value="{{ $em }}" @selected(request('emirate') === $em)>{{ $em }}</option>
        @endforeach
      </select>
    @endif
    @if($cities->count())
      <select name="city">
        <option value="">All Cities</option>
        @foreach($cities as $c)
          <option value="{{ $c }}" @selected(request('city') === $c)>{{ $c }}</option>
        @endforeach
      </select>
    @endif
    @if($categories->count())
      <select name="category_id">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" @selected((string)request('category_id')===(string)$cat->id)>{{ $cat->name }}</option>
        @endforeach
      </select>
    @endif
    <select name="sort">
      <option value="soon" @selected(request('sort','soon')==='soon')>Soonest</option>
      <option value="new"  @selected(request('sort')==='new')>Newest</option>
    </select>
    <button class="btn btn-primary" type="submit">Apply</button>
    <a class="btn" href="{{ route('opportunities.index') }}">Reset</a>
  </form>
  <div class="grid">
    @forelse($opps as $o)
      <article class="card">
        <h3>
          <a href="{{ url('/opportunities/'.$o->id.'-'.\Illuminate\Support\Str::slug($o->title ?? 'opportunity')) }}">
            {{ $o->title ?? 'Opportunity #'.$o->id }}
          </a>
        </h3>
        <p class="muted">
          @if(!empty($o->city)) {{ $o->city }} · @endif
          @if(!empty($o->emirate)) {{ $o->emirate }} @endif
        </p>
        @if(!empty($o->start_at))
          <p class="muted">Starts: {{ \Illuminate\Support\Carbon::parse($o->start_at)->toFormattedDateString() }}</p>
        @endif
        <a class="btn btn-sm" href="{{ url('/opportunities/'.$o->id.'-'.\Illuminate\Support\Str::slug($o->title ?? 'opportunity')) }}">View</a>
      </article>
    @empty
      <p class="muted">No results.</p>
    @endforelse
  </div>
  <div class="pagination">
    {{ $opps->links() }}
  </div>
@endsection
BLADE
echo "[OK] opportunities/index updated"

SHOW="resources/views/public/opportunities/show.blade.php"
cp -a "$SHOW" "$SHOW.bak.$TS" 2>/dev/null || true
cat > "$SHOW" <<'BLADE'
@extends('public.layout')
@php
  $breadcrumbs = [
    ['name'=>'Home','url'=>url('/')],
    ['name'=>'Opportunities','url'=>route('opportunities.index')],
    ['name'=>($row->title ?? ('Opportunity #'.$row->id)),'url'=>url()->current()],
  ];
@endphp
@section('content')
  <article class="detail">
    <h1>{{ $row->title ?? ('Opportunity #'.$row->id) }}</h1>
    <div class="meta muted">
      @if(!empty($row->organization_id))
        <span>Organization #{{ $row->organization_id }}</span>
      @endif
      @if(!empty($row->city) || !empty($row->emirate))
        <span> · {{ trim(($row->city ?? '').' '.($row->emirate ?? '')) }}</span>
      @endif
      @if(!empty($row->start_at))
        <span> · Starts: {{ \Illuminate\Support\Carbon::parse($row->start_at)->toDayDateTimeString() }}</span>
      @endif
    </div>
    @if(!empty($row->description))
      <div class="prose">{!! $row->description !!}</div>
    @endif
    <div class="actions">
      <a class="btn btn-primary" href="{{ route('login') }}">Apply / Sign in</a>
      <a class="btn" href="{{ route('opportunities.index') }}">Back to list</a>
    </div>
  </article>
@endsection
BLADE
echo "[OK] opportunities/show updated"

# --- Localized sitemaps command ---
mkdir -p app/Console/Commands public/sitemaps
CMD="app/Console/Commands/BuildSitemaps.php"
cp -a "$CMD" "$CMD.bak.$TS" 2>/dev/null || true
cat > "$CMD" <<'PHP'
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class BuildSitemaps extends Command
{
    protected $signature = 'swaed:build-sitemaps';
    protected $description = 'Generate localized sitemap index + section sitemaps';

    public function handle(): int
    {
        $base = rtrim(config('app.url', 'https://swaeduae.ae'), '/');
        $publicPath = public_path('sitemaps');
        if (!is_dir($publicPath)) { @mkdir($publicPath, 0775, true); }
        $locales = ['ar','en']; $now = now();

        $built = [];
        foreach ($locales as $loc) {
            $smap = Sitemap::create();
            $smap->add(Url::create("{$base}/?lang={$loc}")->setLastModificationDate($now));
            $smap->add(Url::create("{$base}/opportunities?lang={$loc}")->setLastModificationDate($now));

            if (Schema::hasTable('opportunities')) {
                $rows = DB::table('opportunities')->select('id','title','updated_at')->orderByDesc('updated_at')->limit(10000)->get();
                foreach ($rows as $r) {
                    $slug = Str::slug($r->title ?? "opportunity-{$r->id}");
                    $url  = "{$base}/opportunities/{$r->id}-{$slug}?lang={$loc}";
                    $smap->add(Url::create($url)->setLastModificationDate($r->updated_at ?? $now)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)->setPriority(0.8));
                }
            }
            if (Schema::hasTable('events')) {
                $rows = DB::table('events')->select('id','title','updated_at')->orderByDesc('updated_at')->limit(10000)->get();
                foreach ($rows as $r) {
                    $slug = Str::slug($r->title ?? "event-{$r->id}");
                    $url  = "{$base}/events/{$r->id}-{$slug}?lang={$loc}";
                    $smap->add(Url::create($url)->setLastModificationDate($r->updated_at ?? $now)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)->setPriority(0.6));
                }
            }
            if (Schema::hasTable('organizations')) {
                $rows = DB::table('organizations')->select('id','name','updated_at')->orderByDesc('updated_at')->limit(10000)->get();
                foreach ($rows as $r) {
                    $slug = Str::slug($r->name ?? "org-{$r->id}");
                    $url  = "{$base}/organizations/{$r->id}-{$slug}?lang={$loc}";
                    $smap->add(Url::create($url)->setLastModificationDate($r->updated_at ?? $now)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)->setPriority(0.5));
                }
            }

            $file = public_path("sitemaps/sitemap-{$loc}.xml");
            $smap->writeToFile($file);
            $built[] = ['loc' => "{$base}/sitemaps/sitemap-{$loc}.xml", 'lastmod' => $now];
        }

        $index = SitemapIndex::create();
        foreach ($built as $b) { $index->add($b['loc'], $b['lastmod']); }
        $indexFile = public_path('sitemaps/sitemap-index.xml');
        $index->writeToFile($indexFile);
        copy($indexFile, public_path('sitemap.xml'));
        $this->info("Sitemaps built.");
        return self::SUCCESS;
    }
}
PHP
echo "[OK] BuildSitemaps command written"

# --- Scheduler injection (no fragile sed) ---
SCHED="tools/_inject_schedule.php"
cat > "$SCHED" <<'PHP'
<?php
$k="app/Console/Kernel.php"; $s=file_get_contents($k);
if (strpos($s,"protected function schedule(")===false) { exit(0); }
if (strpos($s,"sitemap:generate")===false) {
  $s=preg_replace("/(protected function schedule\\(.*?\\)\\s*\\{)/s", "$1\n        \$schedule->command('sitemap:generate')->dailyAt('02:30');", $s, 1);
}
if (strpos($s,"swaed:build-sitemaps")===false) {
  $s=preg_replace("/(protected function schedule\\(.*?\\)\\s*\\{)/s", "$1\n        \$schedule->command('swaed:build-sitemaps')->dailyAt('02:35');", $s, 1);
}
file_put_contents($k,$s);
echo "[OK] Scheduler patched\n";
PHP
cp -a app/Console/Kernel.php "app/Console/Kernel.php.bak.$TS"
$PHP "$SCHED"

# --- robots.txt -> sitemap index ---
cp -a "$ROOT/robots.txt" "$ROOT/robots.txt.bak.$TS" 2>/dev/null || true
cat > "$ROOT/robots.txt" <<'ROBOTS'
User-agent: *
Disallow:
Sitemap: https://swaeduae.ae/sitemaps/sitemap-index.xml
ROBOTS
echo "[OK] robots.txt updated"

# --- caches + build sitemaps now ---
$PHP artisan optimize:clear
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan swaed:build-sitemaps || true

echo "---- Sitemaps ----"
ls -lh public/sitemaps/ || true
echo "== Phase 4 done =="
