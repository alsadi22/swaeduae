@once
@php
  $site = config('app.name', 'SwaedUAE');
  $url  = url()->current();
  $title = trim($__env->yieldContent('title', $site));
  $desc  = trim($__env->yieldContent('meta_description', 'Volunteer opportunities across the UAE.'));
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $desc }}">
<link rel="canonical" href="{{ $url }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $site }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_AR' : 'en_US' }}">
<meta name="twitter:card" content="summary_large_image">
@endonce
