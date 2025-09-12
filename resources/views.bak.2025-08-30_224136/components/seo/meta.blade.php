<!-- SEO meta -->
@php($r = optional(Route::current())->getName())
@php($seo = config('seo.pages.'.($r ?? ''), []))
@php($base = rtrim(config('app.url','https://swaeduae.ae'),'/'))
<title>{{ trim(view()->yieldContent('title')) ?: ($seo['title'] ?? config('app.name')) }}</title>
<meta name="description" content="{{ trim(view()->yieldContent('meta_description')) ?: ($seo['description'] ?? 'Community services and initiatives in the UAE') }}">
<link rel="canonical" href="{{ url()->current() }}" />
<meta property="og:title" content="{{ trim(view()->yieldContent('title')) ?: ($seo['title'] ?? config('app.name')) }}">
<meta property="og:description" content="{{ trim(view()->yieldContent('meta_description')) ?: ($seo['description'] ?? 'Community services and initiatives in the UAE') }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:image" content="{{ $base }}/social-card.png">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="{{ $base }}/social-card.png">
