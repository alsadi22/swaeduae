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

{{-- === Certificate Actions (Download / Resend / Revoke) === --}}
@if(isset($rows) || isset($certs))
  @php $list = $rows ?? $certs ?? []; @endphp
  <div class="container py-3" id="cert-actions">
    <h2 class="h5 mb-2">Certificate Actions</h2>
    <ul class="list-unstyled">
      @foreach($list as $row)
        <li class="mb-2">
          <a href="{{ route('certificates.download', $row->id) }}">Download PDF</a>
          <form method="POST" action="{{ route('certificates.resend', $row->id) }}" style="display:inline">@csrf
            <button type="submit">Resend</button>
          </form>
          @role('admin')
          <form method="POST" action="{{ route('certificates.revoke', $row->id) }}" style="display:inline">@csrf
            <button type="submit" onclick="return confirm('Revoke this certificate?')">Revoke</button>
          </form>
          @endrole
          <span class="text-muted" style="margin-left:10px">#{{ $row->id }} — {{ $row->code ?? '' }}</span>
        </li>
      @endforeach
    </ul>
  </div>
@endif
