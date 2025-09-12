@extends('admin.layout')
@section('content')
<h3 class="mb-3">{{ __('Edit Category') }}</h3>
<form method="post" action="{{ route('admin.categories.update',$item->id) }}" class="card p-3">
  @csrf @method('PUT')
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">{{ __('Name') }}</label><input name="name" class="form-control" value="{{ $item->name ?? $item->title }}" required></div>
    <div class="col-md-6"><label class="form-label">{{ __('Slug') }}</label><input name="slug" class="form-control" value="{{ $item->slug }}"></div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary">{{ __('Update') }}</button>
    <form method="post" action="{{ route('admin.categories.destroy',$item->id) }}" onsubmit="return confirm('Delete?')">
      @csrf @method('DELETE')
      <button class="btn btn-outline-danger">{{ __('Delete') }}</button>
    </form>
  </div>
</form>
@endsection
