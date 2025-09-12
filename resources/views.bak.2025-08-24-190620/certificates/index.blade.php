@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('My Certificates') }}</h1>

  @if($items->count())
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>{{ __('Issued') }}</th>
            <th>{{ __('Opportunity') }}</th>
            <th>{{ __('Title') }}</th>
            <th>{{ __('Code') }}</th>
            <th class="text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
        @foreach($items as $c)
          @php
            $issued = $c->issued_at ?? $c->issued_date ?? null;
            try { $issuedFmt = $issued ? \Carbon\Carbon::parse($issued)->format('d M Y') : null; }
            catch (\Throwable $e) { $issuedFmt = $issued; }
          @endphp
          <tr>
            <td>{{ $issuedFmt }}</td>
            <td>{{ $c->opportunity_title ?? '—' }}</td>
            <td>{{ $c->title ?? 'Certificate' }}</td>
            <td><code>{{ $c->code }}</code></td>
            <td class="text-end">
              <a class="btn btn-sm btn-primary" href="{{ $c->file_path }}" target="_blank" rel="noopener">{{ __('Download') }}</a>
              <a class="btn btn-sm btn-outline-secondary" href="{{ url('verify/'.$c->code) }}" target="_blank" rel="noopener">{{ __('Verify') }}</a>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $items->links() }}
    </div>
  @else
    <div class="alert alert-info">{{ __('No certificates yet.') }}</div>
  @endif
</div>
@endsection
