@extends('admin.layout')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">{{ __('Organizations') }}</h3>
    <div class="d-flex gap-2">
      <form class="d-flex" method="get" action="{{ route('admin.organizations.index') }}">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control me-2" placeholder="{{ __('Search name, email, phone') }}">
        <button class="btn btn-primary">{{ __('Search') }}</button>
      </form>
      @if($exists)
        <a href="{{ route('admin.organizations.create') }}" class="btn btn-success">{{ __('Add Organization') }}</a>
      @endif
    </div>
  </div>

  @if(!$exists)
    <div class="alert alert-warning">{{ __("Table 'organizations' not found. Create the table to manage records.") }}</div>
  @else
    <div class="card">
      <div class="table-responsive">
        <table class="table mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th><th>{{ __('Name') }}</th><th>{{ __('Email') }}</th><th>{{ __('Phone') }}</th><th>{{ __('Created') }}</th><th class="text-end">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
          @forelse($rows as $r)
            <tr>
              <td>{{ $r->id }}</td>
              <td>{{ $r->name ?? '-' }}</td>
              <td>{{ $r->email ?? '-' }}</td>
              <td>{{ $r->phone ?? '-' }}</td>
              <td>{{ isset($r->created_at) ? \Illuminate\Support\Carbon::parse($r->created_at)->format('Y-m-d') : '-' }}</td>
              <td class="text-end">
                <a href="{{ route('admin.organizations.edit',$r->id) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                <form action="{{ route('admin.organizations.destroy',$r->id) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Delete this item?') }}')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No data.') }}</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-body">{{ $rows->links('pagination::bootstrap-5') }}</div>
    </div>
  @endif
@endsection
