@php($op = $op ?? ($opportunity ?? null))
@csrf
@if($op && $op->exists)
    @method('PUT')
@endif

<div class="card p-4 space-y-4">
  <div>
    <label class="form-label">{{ __('Title') }}</label>
    <input type="text" name="title" class="form-control"
           value="{{ old('title', $op->title ?? '') }}" required>
    @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    <div>
      <label class="form-label">{{ __('Category') }}</label>
      <input type="text" name="category" class="form-control"
             value="{{ old('category', $op->category ?? '') }}">
      @error('category')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="form-label">{{ __('City') }}</label>
      <input type="text" name="city" class="form-control"
             value="{{ old('city', $op->city ?? '') }}">
      @error('city')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>

  <div>
    <label class="form-label">{{ __('Location') }}</label>
    <input type="text" name="location" class="form-control"
           value="{{ old('location', $op->location ?? '') }}">
    @error('location')<div class="text-danger small">{{ $message }}</div>@enderror
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    <div>
      <label class="form-label">{{ __('Starts at') }}</label>
      <input type="datetime-local" name="starts_at" class="form-control"
             value="{{ old('starts_at', isset($op->starts_at) ? $op->starts_at->format('Y-m-d\TH:i') : '') }}">
      @error('starts_at')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="form-label">{{ __('Ends at') }}</label>
      <input type="datetime-local" name="ends_at" class="form-control"
             value="{{ old('ends_at', isset($op->ends_at) ? $op->ends_at->format('Y-m-d\TH:i') : '') }}">
      @error('ends_at')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
  </div>

  <div>
    <label class="form-label">{{ __('Description') }}</label>
    <textarea name="description" rows="5" class="form-control">{{ old('description', $op->description ?? '') }}</textarea>
    @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
  </div>

  <div class="pt-2">
    <button class="btn btn-teal">
      {{ $op && $op->exists ? __('Update opportunity') : __('Create opportunity') }}
    </button>
    <a href="{{ route('org.opps.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
  </div>
</div>
