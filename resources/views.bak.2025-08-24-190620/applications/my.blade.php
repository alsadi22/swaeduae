@extends('layouts.app')
@section('content')
<div class="container">
  <h3 class="mb-3">{{ __('My Applications') }}</h3>
  <div class="card">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr><th>#</th><th>{{ __('Opportunity') }}</th><th>{{ __('Status') }}</th><th>{{ __('Applied At') }}</th></tr>
        </thead>
        <tbody>
        @forelse($rows as $a)
          <tr>
            <td>{{ $a->id }}</td>
            <td><a href="{{ route('opportunities.show',$a->oid) }}">#{{ $a->oid }}</a></td>
            <td>{{ ucfirst($a->status) }}</td>
            <td>{{ $a->created_at }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted py-4">{{ __('No applications yet.') }}</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body">{{ $rows->links('pagination::bootstrap-5') }}</div>
  </div>
</div>
@endsection
