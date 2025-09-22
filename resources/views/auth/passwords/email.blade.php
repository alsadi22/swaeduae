    : (\Illuminate\Support\Facades\View::exists('public') ? 'public' : 'layouts.app'))
@section('title', __('Forgot Password'))
@section('content')
<div class="container py-5" style="max-width:620px">
  <div class="card shadow-sm border-0" style="border-radius:16px">
    <div class="card-body p-4">
      <h1 class="h4 mb-3 text-center">@lang('Reset your password')</h1>
      @if (session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
      @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" action="{{ route('password.email') }}" class="row g-3">
        @csrf
        <div class="col-12">
          <label class="form-label">@lang('Email')</label>
          <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="col-12"><button class="btn btn-primary w-100">@lang('Email Password Reset Link')</button></div>
      </form>
      <div class="text-center small mt-3"><a href="{{ route('login') }}">@lang('Back to login')</a></div>
    </div>
  </div>
</div>
@endsection
