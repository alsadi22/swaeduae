<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Forgot password — SwaedUAE</title>
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,"Helvetica Neue",Arial,sans-serif;background:#faf7ef;margin:0}
  .wrap{max-width:560px;margin:5rem auto;padding:2rem;background:#fff;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.06)}
  h1{margin:0 0 1rem;font-size:1.6rem}
  label{display:block;margin:.75rem 0 .25rem}
  input{width:100%;padding:.7rem .8rem;border:1px solid #d7d7d7;border-radius:10px}
  .btn{display:inline-block;margin-top:1rem;padding:.7rem 1rem;border-radius:10px;border:0;background:#1e66f5;color:#fff;cursor:pointer}
  .ok{background:#f1f8ff;color:#084298;padding:.6rem .8rem;border-radius:10px;margin:.5rem 0}
</style></head><body>
  <div class="wrap">
    <h1>Forgot your password?</h1>

    @if (session('status'))
      <div class="ok">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <label>Email</label>
      <input type="email" name="email" value="{{ old('email') }}" required autofocus>
      <button class="btn" type="submit">Email reset link</button>
    </form>
  </div>
</body></html>
