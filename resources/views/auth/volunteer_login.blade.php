@extends(\Illuminate\Support\Facades\View::exists('layout-travelpro') ? 'layout-travelpro'
    : (\Illuminate\Support\Facades\View::exists('public') ? 'public' : 'layouts.app'))

@section('content')
<div class="container py-5" style="max-width:820px">
  <div class="card shadow-sm border-0" style="border-radius:16px">
    <div class="card-body p-4">

      {{-- Social buttons (auto-hide if providers disabled) --}}
      @includeIf('auth._social')

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <form method="POST" action="{{ route('login.perform') }}" class="row g-3">
        @csrf
        <div class="col-12">
          <label class="form-label">@lang('Email')</label>
          <input type="email" name="email" value="{{ old('email') }}" required class="form-control" autocomplete="username">
        </div>
        <div class="col-12">
          <label class="form-label">@lang('Password')</label>
          <input type="password" name="password" required class="form-control" autocomplete="current-password">
        </div>
        <div class="col-12 d-flex justify-content-between align-items-center">
          <label class="form-check-label"><input class="form-check-input" type="checkbox" name="remember"> @lang('Remember me')</label>
          <a href="{{ route('password.request') }}" class="small">@lang('Forgot password?')</a>
        </div>
        <div class="col-12">
          <button class="btn btn-primary w-100">@lang('Login')</button>
        </div>
      </form>

      <hr class="my-4">
      <div class="text-center small">
        {{ __('New volunteer?') }} <a href="{{ route('register') }}">{{ __('Create an account') }}</a>
        <br>
        {{ __('Organization?') }}
        <a href="{{ url('/org/register') }}">{{ __('Register Organization') }}</a>
      </div>
    </div>
  </div>
</div>
@endsection
