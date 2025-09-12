@php
  $cfg = config('seo');
  $seo = $seo ?? [];
  $title = trim(($seo['title'] ?? '').' | '.$cfg['site_name']);
  $title = ltrim($title, ' |');
  $desc  = $seo['description'] ?? $cfg['default']['description'];
  $url   = $seo['url'] ?? url()->current();
  $image = $seo['image'] ?? ($cfg['site_url'].'/images/og-default.jpg');
  $canon = $seo['canonical'] ?? $url;
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $desc }}">
<link rel="canonical" href="{{ $canon }}" />
@foreach(($cfg['hreflang'] ?? []) as $hl)
<link rel="alternate" href="{{ $url }}@if(str_contains($url,'?'))&@else?@endif{{ 'lang='.$hl }}" hreflang="{{ $hl }}" />
@endforeach

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:image" content="{{ $image }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $desc }}">
<meta name="twitter:image" content="{{ $image }}">
