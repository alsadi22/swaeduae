@extends('admin.layout')
@section('content')
<h3 class="mb-3">{{ __('Add Category') }}</h3>
<form method="post" action="{{ route('admin.categories.store') }}" class="card p-3">
  @csrf
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">{{ __('Name') }}</label><input name="name" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">{{ __('Slug (optional)') }}</label><input name="slug" class="form-control"></div>
  </div>
  <div class="mt-3"><button class="btn btn-primary">{{ __('Save') }}</button></div>
</form>
@endsection
