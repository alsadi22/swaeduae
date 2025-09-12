<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign in — SwaedUAE</title>
<style>
 body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;background:#0b1220;color:#fff;margin:0}
 .wrap{max-width:420px;margin:10vh auto;padding:2rem;background:#111827;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.4)}
 h1{margin:0 0 1rem}
 label{display:block;margin:.5rem 0 .25rem;color:#cbd5e1}
 input{width:100%;padding:.6rem .7rem;border-radius:10px;border:1px solid #334155;background:#0f172a;color:#e2e8f0}
 .err{background:#7f1d1d;padding:.6rem .8rem;border-radius:10px;margin-bottom:.8rem}
 button{margin-top:1rem;padding:.6rem .9rem;border-radius:10px;background:#1e66f5;color:#fff;border:0;cursor:pointer}
 .row{display:flex;justify-content:space-between;align-items:center;margin-top:.5rem}
 a{color:#93c5fd;text-decoration:none}
</style></head><body>
<div class="wrap">
  <h1>Sign in</h1>
  @if ($errors->any())
    <div class="err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
  @endif
  <form method="POST" action="{{ url('/login') }}">
    @csrf
    <label>Email</label>
    <input type="email" name="email" required autofocus value="{{ old('email') }}">
    <label>Password</label>
    <input type="password" name="password" required>
    <div class="row">
      <label style="display:flex;align-items:center;gap:.4rem">
        <input type="checkbox" name="remember" value="1" style="width:auto"> Remember me
      </label>
      <a href="{{ url('/forgot-password') }}">Forgot?</a>
    </div>
    <button type="submit">Sign in</button>
  </form>
</div>
</body></html>
