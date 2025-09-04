@extends('org.layout')

@section('title', __('My Events'))

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">{{ __('My Events') }}</h1>
    <form method="get" class="d-flex" action="{{ route('org.events.index') }}">
      <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Search title/description') }}">
      <button class="btn btn-primary ms-2">{{ __('Search') }}</button>
    </form>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <table class="table mb-0 align-middle">
        <thead>
          <tr>
            <th>{{ __('#') }}</th>
            <th>{{ __('Title') }}</th>
            <th class="text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
        @forelse($opps as $o)
          <tr>
            <td>{{ $o->id }}</td>
            <td>{{ $o->title }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('org.events.volunteers', $o->id) }}">{{ __('Volunteers') }}</a>
              <a class="btn btn-sm btn-outline-primary" href="{{ route('org.events.export', $o->id) }}">{{ __('Export CSV') }}</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="3" class="text-center text-muted p-4">{{ __('No events found.') }}</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer bg-transparent">
      {{ $opps->links() }}
    </div>
  </div>
</div>
@endsection
