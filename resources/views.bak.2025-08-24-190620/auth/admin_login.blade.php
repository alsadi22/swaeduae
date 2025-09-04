@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <h5 class="mb-3 text-center">{{ __('Admin Login') }}</h5>
          @if (session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
          @if ($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
          @endif
          <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">{{ __('Email') }}</label>
              <input type="email" name="email" class="form-control" required autocomplete="email">
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Password') }}</label>
              <input type="password" name="password" class="form-control" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary w-100">{{ __('Login') }}</button>
            <div class="small text-primary fw-semibold mt-2">{{ __('Note: Only admin accounts can access /admin.') }}</div>
<div class="text-center text-sm mt-2">@if(\Illuminate\Support\Facades\Route::has('password.request'))<a href="{{ route('password.request') }}">Forgot your password?</a>@else<a href="/forgot-password">Forgot your password?</a>@endif</div>
</form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
