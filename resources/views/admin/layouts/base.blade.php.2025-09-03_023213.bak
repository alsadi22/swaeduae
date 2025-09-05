@extends("layouts.admin-argon")
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Admin')</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand" href="{{ route('admin.root') }}">Admin</a>
    <div class="ms-auto">
      @auth('admin')
        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">@csrf
          <button class="btn btn-sm btn-outline-secondary">Logout</button>
        </form>
      @endauth
    </div>
  </div>
</nav>
<div class="container">
  @if (session('toast'))
    <div class="alert alert-info my-3">{{ session('toast') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger my-3">{{ $errors->first() }}</div>
  @endif
</div>
@yield('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
