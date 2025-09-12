<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  @includeIf(partials.theme-public)
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $title ?? 'Sign in' }} — {{ config('app.name') }}</title>
</head>
<body class="bg-light">
  <div class="container py-5">@yield('content')</div>
</body>
</html>
