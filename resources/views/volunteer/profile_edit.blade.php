@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Edit Profile</h1>
  <form method="POST" action="{{ route('volunteer.profile.update') }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="{{ old('name',$volunteer->name) }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="{{ old('email',$volunteer->email) }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control" value="{{ old('phone',$volunteer->phone) }}">
    </div>
    <div class="mb-3">
      <label class="form-label">Skills / Interests</label>
      <textarea name="skills" class="form-control">{{ old('skills',$volunteer->skills) }}</textarea>
    </div>
    <button class="btn btn-success">Save</button>
  </form>
</div>
@endsection
