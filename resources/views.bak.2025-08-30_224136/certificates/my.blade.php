@extends('layouts.app')
@section('content')
<div class="container">
  <h3 class="mb-3">{{ __('My Certificates') }}</h3>
  <div class="card">
    <div class="table-responsive">
      <table class="table mb-0 align-middle">
        <thead class="table-light">
          <tr><th>#</th><th>{{ __('Code') }}</th><th>{{ __('Opportunity') }}</th><th>{{ __('Hours') }}</th><th>{{ __('Issued At') }}</th><th class="text-end">{{ __('Download') }}</th></tr>
        </thead>
        <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r->id }}</td>
            <td>{{ $r->code }}</td>
            <td>{{ $r->opp_title ?? '-' }}</td>
            <td>{{ number_format($r->hours,2) }}</td>
            <td>{{ $r->created_at }}</td>
            <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('certificates.pdf',$r->uuid) }}">{{ __('PDF') }}</a></td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No certificates yet.') }}</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body d-flex justify-content-between">
      <div>{{ $rows->links('pagination::bootstrap-5') }}</div>
      <a class="btn btn-success" href="{{ route('transcript.pdf') }}">{{ __('Download Transcript PDF') }}</a>
    </div>
  </div>
</div>
@endsection
