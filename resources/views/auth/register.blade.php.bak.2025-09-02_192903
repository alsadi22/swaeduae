@extends('layouts.auth')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h1 class="h4 mb-3">Create account</h1>
        <form method="POST" action="{{ route('register') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" required>
          </div>
          <div class="mb-4">
            <label class="form-label">Confirm password</label>
            <input name="password_confirmation" type="password" class="form-control" required>
          </div>
          <button class="btn btn-primary w-100" type="submit">Register</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
