@extends("layouts.admin-argon")
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Sign In</title></head>
<body style="font-family:system-ui;max-width:520px;margin:64px auto">
  <h2>Admin Sign In</h2>
  @if ($errors->any())
    <div style="color:#b00020;margin:8px 0">
      @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
  @endif
  <form method="POST" action="{{ route('admin.login.post') }}" style="display:grid;gap:12px">
    @csrf
    <label>Email <input name="email" type="email" required autocomplete="username" style="width:100%;padding:8px"></label>
    <label>Password <input name="password" type="password" required autocomplete="current-password" style="width:100%;padding:8px"></label>
    <label style="display:flex;align-items:center;gap:6px">
      <input name="remember" type="checkbox" value="1"> Remember me
    </label>
    <button type="submit" style="padding:10px 16px">Sign in</button>
  </form>
</body>
</html>
