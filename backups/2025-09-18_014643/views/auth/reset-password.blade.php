<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reset password — SwaedUAE</title>
<style>
 body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;background:#0b1220;color:#fff;margin:0}
 .wrap{max-width:480px;margin:10vh auto;padding:2rem;background:#111827;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.4)}
 h1{margin:0 0 1rem}
 label{display:block;margin:.5rem 0 .25rem;color:#cbd5e1}
 input{width:100%;padding:.6rem .7rem;border-radius:10px;border:1px solid #334155;background:#0f172a;color:#e2e8f0}
 .err{background:#7f1d1d;padding:.6rem .8rem;border-radius:10px;margin-bottom:.8rem}
 .ok{background:#065f46;padding:.6rem .8rem;border-radius:10px;margin-bottom:.8rem}
 button{margin-top:1rem;padding:.6rem .9rem;border-radius:10px;background:#16a34a;color:#fff;border:0;cursor:pointer}
</style></head><body>
<div class="wrap">
  <h1>Reset password</h1>

  @if (session('status')) <div class="ok">{{ session('status') }}</div> @endif
  @if ($errors->any()) <div class="err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div> @endif

  <form method="POST" action="{{ url('/reset-password') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <label>Email</label>
    <input type="email" name="email" required value="{{ old('email', $email ?? '') }}">
    <label>New password</label>
    <input type="password" name="password" required>
    <label>Confirm password</label>
    <input type="password" name="password_confirmation" required>
    <button type="submit">Update password</button>
  </form>
</div>
</body></html>
