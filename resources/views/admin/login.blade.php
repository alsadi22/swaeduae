@extends('admin/layouts/argon')
@section('content')

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


@endsection
