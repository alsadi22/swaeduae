@extends('admin.layout')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">{{ __('Learning Requests') }}</h3>
    <form class="d-flex" method="get" action="{{ route('admin.learning.index') }}">
      <input type="text" name="q" value="{{ request('q') }}" class="form-control me-2" placeholder="{{ __('Search') }}">
      <button class="btn btn-primary">{{ __('Search') }}</button>
    </form>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th><th>{{ __('User') }}</th><th>{{ __('Title') }}</th><th>{{ __('Status') }}</th><th>{{ __('Created') }}</th><th class="text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
        @foreach($rows as $r)
          <tr>
            <td>{{ $r->id }}</td>
            <td>{{ $r->user_id }}</td>
            <td>{{ $r->title }}</td>
            <td>{{ ucfirst($r->status) }}</td>
            <td>{{ $r->created_at }}</td>
            <td class="text-end">
              <form method="post" action="{{ route('admin.learning.update',$r->id) }}" class="d-inline">
                @csrf
                <select name="status" class="form-select d-inline w-auto">
                  @foreach(['pending','approved','rejected'] as $s)
                    <option value="{{ $s }}" @selected($r->status===$s)>{{ ucfirst($s) }}</option>
                  @endforeach
                </select>
                <button class="btn btn-sm btn-outline-primary">{{ __('Save') }}</button>
              </form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-body">{{ $rows->links('pagination::bootstrap-5') }}</div>
  </div>
@endsection
