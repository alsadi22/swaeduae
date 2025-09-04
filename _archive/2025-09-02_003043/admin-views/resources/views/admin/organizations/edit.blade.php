@extends('admin.layout')
@section('content')
<h3 class="mb-3">{{ __('Edit Organization') }}</h3>
<form method="post" action="{{ route('admin.organizations.update',$org->id) }}" class="card p-3">
  @csrf @method('PUT')
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">{{ __('Name') }}</label><input name="name" class="form-control" value="{{ $org->name }}" required></div>
    <div class="col-md-6"><label class="form-label">{{ __('Email') }}</label><input name="email" type="email" class="form-control" value="{{ $org->email }}"></div>
    <div class="col-md-6"><label class="form-label">{{ __('Phone') }}</label><input name="phone" class="form-control" value="{{ $org->phone }}"></div>
    <div class="col-md-6"><label class="form-label">{{ __('Website') }}</label><input name="website" class="form-control" value="{{ $org->website }}"></div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary">{{ __('Update') }}</button>
    <form method="post" action="{{ route('admin.organizations.destroy',$org->id) }}" onsubmit="return confirm('Delete?')">
      @csrf @method('DELETE')
      <button class="btn btn-outline-danger">{{ __('Delete') }}</button>
    </form>
  </div>
</form>
@endsection
