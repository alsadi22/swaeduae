@extends('admin.layout')
@section('content')
<h3 class="mb-3">{{ __('Add Event') }}</h3>
<form method="post" action="{{ route('admin.events.store') }}" class="card p-3">
  @csrf
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">{{ __('Title') }}</label><input name="title" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">{{ __('Location') }}</label><input name="location" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">{{ __('Date') }}</label><input type="date" name="date" class="form-control"></div>
    <div class="col-12"><label class="form-label">{{ __('Description') }}</label><textarea name="description" class="form-control" rows="5"></textarea></div>
  </div>
  <div class="mt-3"><button class="btn btn-primary">{{ __('Save') }}</button></div>
</form>
@endsection
