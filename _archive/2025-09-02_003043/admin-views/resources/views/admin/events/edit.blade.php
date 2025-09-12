@extends('admin.layout')
@section('content')
<h3 class="mb-3">{{ __('Edit Event') }}</h3>
<form method="post" action="{{ route('admin.events.update',$item->id) }}" class="card p-3">
  @csrf @method('PUT')
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">{{ __('Title') }}</label><input name="title" class="form-control" value="{{ $item->title }}" required></div>
    <div class="col-md-6"><label class="form-label">{{ __('Location') }}</label><input name="location" class="form-control" value="{{ $item->location }}"></div>
    <div class="col-md-6"><label class="form-label">{{ __('Date') }}</label><input type="date" name="date" class="form-control" value="{{ $item->date }}"></div>
    <div class="col-12"><label class="form-label">{{ __('Description') }}</label><textarea name="description" class="form-control" rows="5">{{ $item->description }}</textarea></div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary">{{ __('Update') }}</button>
    <form method="post" action="{{ route('admin.events.destroy',$item->id) }}" onsubmit="return confirm('Delete?')">
      @csrf @method('DELETE')
      <button class="btn btn-outline-danger">{{ __('Delete') }}</button>
    </form>
  </div>
</form>
@endsection
