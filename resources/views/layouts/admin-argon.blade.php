<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Admin Login')</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/argon-dashboard@2.0.5/assets/css/argon-dashboard.min.css">
</head>
<body class="g-sidenav-show bg-gray-100">
  <main class="container py-5">@yield('content')</main>
</body>
</html>
