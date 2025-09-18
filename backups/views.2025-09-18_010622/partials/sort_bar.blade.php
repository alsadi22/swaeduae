@php
  $current = request('sort', 'newest');
  $qs = request()->except('sort', 'page');
@endphp
<div class="container mt-3">
  <form method="GET" action="" class="row g-2 align-items-center">
    @foreach($qs as $k => $v)
      <input type="hidden" name="{{ $k }}" value="{{ is_array($v) ? implode(',', $v) : $v }}">
    @endforeach
    <div class="col-auto">
      <label class="col-form-label">{{ __('Sort by') }}</label>
    </div>
    <div class="col-auto">
      <select name="sort" class="form-select" onchange="this.form.submit()">
        <option value="newest" {{ $current==='newest' ? 'selected' : '' }}>{{ __('Newest') }}</option>
        <option value="closing_soon" {{ $current==='closing_soon' ? 'selected' : '' }}>{{ __('Closing soon') }}</option>
      </select>
    </div>
  </form>
</div>
