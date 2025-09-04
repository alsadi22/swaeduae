@php
  $cats = \App\Models\Opportunity::query()->whereNotNull('category')->select('category')->distinct()->orderBy('category')->pluck('category');
  $regions = \App\Models\Opportunity::query()->whereNotNull('region')->select('region')->distinct()->orderBy('region')->pluck('region');
@endphp
<form class="opps-search mb-3" action="{{ route('public.opportunities') }}" method="get">
  <div class="row g-2 align-items-end">
    <div class="col-md-3">
      <label class="form-label small">{{ __('Category') }}</label>
      <select class="form-select" name="category">
        <option value="">{{ __('All') }}</option>
        @foreach($cats as $c)<option value="{{ $c }}" @selected(request('category')==$c)>{{ $c }}</option>@endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label small">{{ __('Region/Emirate') }}</label>
      <select class="form-select" name="region">
        <option value="">{{ __('All') }}</option>
        @foreach($regions as $r)<option value="{{ $r }}" @selected(request('region')==$r)>{{ $r }}</option>@endforeach
      </select>
    </div>
    <div class="col-md">
      <label class="form-label small">{{ __('Search') }}</label>
      <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Keyword, skill, cause') }}">
    </div>
    <div class="col-auto">
      <button class="btn btn-primary">{{ __('Apply Filters') }}</button>
    </div>
  </div>
</form>
