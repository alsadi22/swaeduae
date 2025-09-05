@php
  $siteName = $appSettings['site_name'] ?? 'SawaedUAE';
  $seoTitle = $appSettings['seo_title'] ?? $siteName;
  $seoDesc  = $appSettings['seo_description'] ?? '';
  $logoPath = $appSettings['logo'] ?? null;
  $favicon  = $appSettings['favicon'] ?? null;
  $logoUrl  = $logoPath ? asset('storage/'.$logoPath) : null;
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDesc }}"/>

@if($favicon)<link rel="icon" href="{{ asset('storage/'.$favicon) }}">@endif

<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDesc }}">
@if($logoUrl)<meta property="og:image" content="{{ $logoUrl }}">@endif
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDesc }}">
@if($logoUrl)<meta name="twitter:image" content="{{ $logoUrl }}">@endif
