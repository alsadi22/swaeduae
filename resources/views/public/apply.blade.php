@extends('public.layout')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('Apply') }}</h1>
  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  <form method="POST" action="{{ route('apply.store') }}" class="card p-3">
    @csrf
    <input type="hidden" name="target_type" value="{{ $targetType }}">
    <input type="hidden" name="target_id" value="{{ $targetId }}">
    <div class="mb-3">
      <label class="form-label">{{ __('Name') }}</label>
      <input name="name" class="form-control" required>
      @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Email') }}</label>
      <input type="email" name="email" class="form-control" required>
      @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Phone') }}</label>
      <input name="phone" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Message') }}</label>
      <textarea name="message" class="form-control" rows="3"></textarea>
    </div>
    <button class="btn btn-primary">{{ __('Submit') }}</button>
  </form>
</div>
@endsection
