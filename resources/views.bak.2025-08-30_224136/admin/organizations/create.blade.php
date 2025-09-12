@extends('admin.layout')
@section('content')
<h3 class="mb-3">{{ __('Add Organization') }}</h3>
<form method="post" action="{{ route('admin.organizations.store') }}" class="card p-3">
  @csrf
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">{{ __('Name') }}</label><input name="name" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">{{ __('Email') }}</label><input name="email" type="email" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">{{ __('Phone') }}</label><input name="phone" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">{{ __('Website') }}</label><input name="website" class="form-control" placeholder="https://"></div>
  </div>
  <div class="mt-3"><button class="btn btn-primary">{{ __('Save') }}</button></div>
</form>
@endsection
