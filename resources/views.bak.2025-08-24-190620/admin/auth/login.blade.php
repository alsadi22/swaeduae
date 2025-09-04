<!DOCTYPE html><html lang="en" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login</title>
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head><body class="p-4 bg-light">
<div class="container" style="max-width:480px">
  <h1 class="h4 mb-3 text-center">Admin Login</h1>

  @php($errs = session('errors'))
  @if ($errs && $errs->any())
    <div class="alert alert-danger"><ul class="mb-0">
      @foreach ($errs->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul></div>
  @endif

  <form method="POST" action="{{ route('admin.login.post') }}">@csrf
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input name="email" type="email" class="form-control" required value="{{ old('email') }}">
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input name="password" type="password" class="form-control" required>
    </div>
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="remember" id="remember">
      <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <button class="btn btn-primary w-100" type="submit">Sign in</button>
  </form>
</div>
</body></html>
