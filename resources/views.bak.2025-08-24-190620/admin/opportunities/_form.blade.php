<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ $action }}">
      @csrf
      @if($method === 'PUT') @method('PUT') @endif
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">{{ __('Title') }}</label>
          <input class="form-control" name="title" value="{{ old('title', $item->title ?? '') }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">{{ __('Category') }}</label>
          <input class="form-control" name="category" value="{{ old('category', $item->category ?? '') }}">
        </div>
        <div class="col-12">
          <label class="form-label">{{ __('Description') }}</label>
          <textarea class="form-control" rows="6" name="description" required>{{ old('description', $item->description ?? '') }}</textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">{{ __('Region') }}</label>
          <input class="form-control" name="region" value="{{ old('region', $item->region ?? '') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">{{ __('Location') }}</label>
          <input class="form-control" name="location" value="{{ old('location', $item->location ?? '') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label">{{ __('Start Date') }}</label>
          <input type="date" class="form-control" name="start_date" value="{{ old('start_date', (isset($item->start_date)? \Illuminate\Support\Carbon::parse($item->start_date)->toDateString():'')) }}">
        </div>
        <div class="col-md-2">
          <label class="form-label">{{ __('End Date') }}</label>
          <input type="date" class="form-control" name="end_date" value="{{ old('end_date', (isset($item->end_date)? \Illuminate\Support\Carbon::parse($item->end_date)->toDateString():'')) }}">
        </div>
        <div class="col-12 d-grid">
          <button class="btn btn-teal">{{ __('Save') }}</button>
        </div>
      </div>
    </form>
  </div>
</div>
