<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','SwaedUAE')</title>
  <meta name="description" content="@yield('meta_description','Volunteer opportunities across the UAE')">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/assets/app.css">
  @stack('head')
</head>
<body class="bg-[var(--bg)] text-[var(--ink)] font-[Inter] antialiased">
  @include('public.components.nav')
  <main class="min-h-[70vh]">
    @yield('content')
  </main>
  @include('public.components.footer')
  @stack('scripts')
</body>
</html>
