<div class="card mb-3 shadow-sm">
  <div class="card-body">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">{{ __('Search') }}</label>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Search...') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('Sort By') }}</label>
        <select name="sort" class="form-select">
          <option value="recent" {{ request('sort')=='recent'?'selected':'' }}>{{ __('Most Recent') }}</option>
          <option value="oldest" {{ request('sort')=='oldest'?'selected':'' }}>{{ __('Oldest') }}</option>
          <option value="name_asc" {{ request('sort')=='name_asc'?'selected':'' }}>{{ __('Name ↑') }}</option>
          <option value="name_desc" {{ request('sort')=='name_desc'?'selected':'' }}>{{ __('Name ↓') }}</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">{{ __('Per Page') }}</label>
        <select name="per_page" class="form-select">
          @foreach([10,25,50,100] as $n)
            <option value="{{ $n }}" {{ request('per_page',25)==$n?'selected':'' }}>{{ $n }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-teal mt-3">{{ __('Apply') }}</button>
        @isset($exportRoute)
          <a href="{{ route($exportRoute, array_filter(['q'=>request('q')])) }}" class="btn btn-outline-dark mt-3">
            {{ __('Export CSV') }}
          </a>
        @endisset
      </div>
    </form>
  </div>
</div>
