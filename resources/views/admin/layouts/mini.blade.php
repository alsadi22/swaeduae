@extends("layouts.admin-argon")
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('page_title','Admin')</title>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body style="font-family:system-ui;background:#f8fafc">
<header style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:#111827;color:#fff">
  <div style="font-weight:700">SwaedUAE Admin</div>
  <form method="POST" action="{{ route('admin.logout') }}" style="margin:0">
    @csrf
    <button type="submit" style="background:#ef4444;border:0;color:#fff;padding:8px 12px;border-radius:6px;cursor:pointer">Logout</button>
  </form>
</header>
<main style="max-width:1100px;margin:24px auto;padding:0 16px">
  @yield('content')
</main>
</body>
</html>
