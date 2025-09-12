@extends('admin.layout')
@section('content')
<h3 class="mb-3">{{ __('Edit Partner') }}</h3>
<form method="post" action="{{ route('admin.partners.update',$partner->id) }}" enctype="multipart/form-data" class="card p-3">
  @csrf @method('PUT')
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">{{ __('Name') }}</label><input name="name" class="form-control" value="{{ $partner->name }}" required></div>
    <div class="col-md-6"><label class="form-label">{{ __('Website') }}</label><input name="website" class="form-control" value="{{ $partner->website }}"></div>
    <div class="col-md-6">
      <label class="form-label">{{ __('Logo') }}</label>
      <input type="file" name="logo" class="form-control">
      @if($partner->logo)<img src="/{{ $partner->logo }}" class="mt-2" style="max-height:60px">@endif
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary">{{ __('Update') }}</button>
    <form method="post" action="{{ route('admin.partners.destroy',$partner->id) }}" onsubmit="return confirm('Delete?')">
      @csrf @method('DELETE')
      <button class="btn btn-outline-danger">{{ __('Delete') }}</button>
    </form>
  </div>
</form>
@endsection
