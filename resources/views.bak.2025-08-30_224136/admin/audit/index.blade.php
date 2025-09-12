@extends('admin.layout')

@section('content')
<div class="container-fluid py-4">
  <h1 class="h4 mb-3">{{ __('Audit Logs') }}</h1>

  <form method="GET" action="" class="card shadow-sm mb-3">
    <div class="card-body row g-2 align-items-end">
      <div class="col-12 col-md-3">
        <label class="form-label">{{ __('Search') }}</label>
        <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('Action, type, note...') }}">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label">{{ __('Entity Type') }}</label>
        <select name="entity_type" class="form-select">
          <option value="">{{ __('Any') }}</option>
          @foreach($types as $t)
            <option value="{{ $t }}" @selected(request('entity_type')===$t)>{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label">{{ __('Entity ID') }}</label>
        <input type="number" class="form-control" name="entity_id" value="{{ request('entity_id') }}">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label">{{ __('Actor ID') }}</label>
        <input type="number" class="form-control" name="actor_id" value="{{ request('actor_id') }}">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label">{{ __('From') }}</label>
        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label">{{ __('To') }}</label>
        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
      </div>
      <div class="col-12 col-md-1 d-flex gap-2">
        <button class="btn btn-primary w-100">{{ __('Filter') }}</button>
      </div>
      <div class="col-12 col-md-2">
        <a class="btn btn-outline-secondary w-100" href="{{ route('admin.audit.export.csv', request()->query()) }}">CSV</a>
      </div>
    </div>
  </form>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>ID</th><th>{{ __('Actor') }}</th><th>{{ __('Action') }}</th>
            <th>{{ __('Entity') }}</th><th>{{ __('Note') }}</th><th>{{ __('When') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $r)
            <tr>
              <td>{{ $r->id }}</td>
              <td>{{ $r->actor_id }}</td>
              <td><code>{{ $r->action }}</code></td>
              <td>{{ $r->entity_type }} #{{ $r->entity_id }}</td>
              <td class="small text-truncate" style="max-width:380px">{{ $r->note }}</td>
              <td><small class="text-muted">{{ \Illuminate\Support\Carbon::parse($r->created_at)->diffForHumans() }}</small></td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted p-4">{{ __('No logs') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body">
      {{ $logs->links() }}
    </div>
  </div>
</div>
@endsection
