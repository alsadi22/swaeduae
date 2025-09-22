@extends('public.layout')
@section('title', $title ?? 'Page')
@section('content')
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create account — SwaedUAE</title>
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,"Helvetica Neue",Arial,sans-serif;background:#faf7ef;margin:0}
  .wrap{max-width:560px;margin:5rem auto;padding:2rem;background:#fff;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.06)}
  h1{margin:0 0 1rem;font-size:1.6rem}
  label{display:block;margin:.75rem 0 .25rem}
  input{width:100%;padding:.7rem .8rem;border:1px solid #d7d7d7;border-radius:10px}
  .btn{display:inline-block;margin-top:1rem;padding:.7rem 1rem;border-radius:10px;border:0;background:#1e66f5;color:#fff;cursor:pointer}
  .error{background:#fff2f0;color:#b02121;padding:.6rem .8rem;border-radius:10px;margin:.5rem 0}
</style></head><body>
  <div class="wrap">
    <h1>Create account</h1>

    @if ($errors->any())
      <div class="error">
        @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
      </div>
    @endif

    <form method="POST" action="{{ url('/register') }}">
      @csrf
      <label>Name</label>
      <input type="text" name="name" value="{{ old('name') }}" required>

      <label>Email</label>
      <input type="email" name="email" value="{{ old('email') }}" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Confirm Password</label>
      <input type="password" name="password_confirmation" required>

      <button class="btn" type="submit">Register</button>
    </form>

    <p style="margin-top:1rem">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>
</body></html>
@endsection
