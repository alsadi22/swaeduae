@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Register your Organization</h1>
  <form method="POST" action="{{ route('organization.register.store') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Organization Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Contact Person</label>
      <input type="text" name="contact_person" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Confirm Password</label>
      <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button class="btn btn-success">Register Organization</button>
  </form>
</div>
@endsection
