

<form method="GET" action="" class="card shadow-sm mb-3">
  <div class="card-body row g-2 align-items-end">
    <div class="col-12 col-md-2">
      <label class="form-label">{{ __('Status') }}</label>
      <select name="status" class="form-select">
        <option value="">{{ __('Any') }}</option>
        @foreach(['approved','pending','waitlist','declined'] as $s)
          <option value="{{ $s }}" @selected(request('status')===$s)>{{ __(ucfirst($s)) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">{{ __('Opportunity') }}</label>
      <select name="opportunity_id" class="form-select">
        <option value="">{{ __('All') }}</option>
        @foreach($opps as $o)
          <option value="{{ $o->id }}" @selected((string)request('opportunity_id')===(string)$o->id)>{{ $o->title ?? ('#'.$o->id) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-6 col-md-2">
      <label class="form-label">{{ __('From') }}</label>
      <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
    </div>
    <div class="col-6 col-md-2">
      <label class="form-label">{{ __('To') }}</label>
      <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
    </div>
    <div class="col-12 col-md-2 d-flex gap-2">
      <button class="btn btn-primary w-100">{{ __('Filter') }}</button>
      <a class="btn btn-outline-secondary" href="{{ $csvUrl }}" title="{{ __('Export CSV with current filters') }}">CSV</a>
    </div>
  </div>
</form>
