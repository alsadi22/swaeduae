@extends('public.layout-travelpro')
@section('title','Sign in')
@section('content')
<section class="section"><div class="container" style="max-width:560px">
  <h2 class="mb-3">Sign in</h2>
  @if ($errors->any())
    <div class="alert alert-danger py-2">Invalid email or password.</div>
  @endif
  <form method="POST" action="{{ url('/login') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
      <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary">Sign in</button>
      <a class="btn btn-outline-secondary" href="{{ url('/reset-password') }}">Forgot password?</a>
    </div>
  </form>
</div></section>
@endsection
