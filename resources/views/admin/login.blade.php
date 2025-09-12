<!doctype html>
<html lang="en" dir="auto">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,"Helvetica Neue",Arial,"Noto Sans",sans-serif;background:#0f172a;color:#e5e7eb;margin:0}
    .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
    .card{width:100%;max-width:440px;background:#111827;border:1px solid #1f2937;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    .card h1{margin:0;padding:24px 24px 0;font-weight:700;font-size:22px}
    .inner{padding:24px}
    label{display:block;margin:12px 0 6px;font-size:14px;color:#cbd5e1}
    input[type="email"],input[type="password"]{width:100%;padding:12px;border-radius:10px;border:1px solid #334155;background:#0b1220;color:#e5e7eb}
    .row{display:flex;align-items:center;justify-content:space-between;margin:10px 0 4px}
    .row label{display:flex;align-items:center;margin:0;font-size:13px}
    .row input[type="checkbox"]{margin-right:8px}
    button{width:100%;margin-top:16px;padding:12px 16px;border:0;border-radius:12px;background:#22c55e;color:#06270f;font-weight:700;cursor:pointer}
    .err{background:#7f1d1d;color:#fecaca;border:1px solid #b91c1c;padding:10px 12px;border-radius:10px;margin:0 0 12px;font-size:13px}
  </style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <div class="inner">
      @if ($errors->any())
        <div class="err">
          <div><strong>Login error</strong></div>
          <ul style="margin:6px 0 0;padding-left:18px">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.login.perform') }}">
        @csrf
      <label for="email">Email</label>
      <input id="email" name="email" type="email" autocomplete="username" required value="{{ old('email') }}">

      <label for="password">Password</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required>

      <div class="row">
        <label><input type="checkbox" name="remember"> Remember me</label>
        <span style="font-size:12px;opacity:.7">SwaedUAE</span>
      </div>

      <button type="submit">Sign in</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
