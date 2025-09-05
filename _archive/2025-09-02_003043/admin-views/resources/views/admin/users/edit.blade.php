@extends('admin.layout')
@section('page-title','Edit User')
@section('content')
<form method="post" action="{{ route('admin.users.update',$user->id) }}" class="card">
  @csrf @method('PUT')
  <div class="card-body">

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Name</label>
        <input name="name" value="{{ old('name',$user->name) }}" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email',$user->email) }}" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">New Password (optional)</label>
        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
      </div>
      <div class="col-md-6 d-flex align-items-end">
        <div class="form-check mt-4">
          <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" value="1" @checked($user->is_admin ?? 0)>
          <label class="form-check-label" for="is_admin">Admin</label>
        </div>
      </div>
    </div>
  </div>
  <div class="card-footer text-end">
    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Cancel</a>
    <button class="btn btn-success">Save</button>
  </div>
</form>
@endsection
