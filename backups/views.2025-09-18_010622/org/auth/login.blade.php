@extends('public.layout')
@section('title','Organization Sign in')
@section('content')
<div class="py-5 bg-light"><div class="container"><div class="row justify-content-center">
<div class="col-xl-4 col-lg-5 col-md-6"><div class="card shadow-sm border-0"><div class="card-body p-4 p-md-5">
<h1 class="h5 mb-4 fw-semibold">Organization Sign in</h1>
@if ($errors->any())<div class="alert alert-danger small"><ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ url('/org/login') }}" class="mb-3">@csrf
  <div class="mb-3"><label class="form-label">Business Email</label><input class="form-control" type="email" name="email" required placeholder="name@yourcompany.com"></div>
  <div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="form-check"><input class="form-check-input" type="checkbox" name="remember" id="orgRemember"><label class="form-check-label" for="orgRemember">Remember Me</label></div>
    <a class="small" href="{{ url('/forgot-password') }}">Forgot password?</a>
  </div>
  <button class="btn btn-primary w-100" type="submit">Sign in</button>
</form>
<p class="small text-muted mb-0">No org account? <a href="{{ url('/org/register') }}">Submit for approval</a></p>
</div></div></div>
</div></div></div>
@endsection
