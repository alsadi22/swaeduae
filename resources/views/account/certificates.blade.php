@extends('public.layout-travelpro')
@section('title','My Certificates')
@section('content')
<section class="section"><div class="container" style="max-width:960px">
  @php($certs = $certs ?? collect())
  <h2 class="mb-3">My Certificates</h2>
  @if($certs->isEmpty())
    <p class="text-muted">No certificates yet.</p>
  @else
  <div class="table-responsive">
  <table class="table table-sm align-middle">
    <thead><tr>
      <th>Certificate</th><th>Issued</th><th>Hours</th><th>Status</th><th>Actions</th>
    </tr></thead>
    <tbody>
      @foreach($certs as $c)
      <tr>
        <td>{{ $c->title ?? 'Certificate' }}</td>
        <td>
          @if(!empty($c->issued_at))
            {{ \Carbon\Carbon::parse($c->issued_at)->format('Y-m-d H:i') }}
          @elseif(!empty($c->awarded_at))
            {{ \Carbon\Carbon::parse($c->awarded_at)->format('Y-m-d H:i') }}
          @else
            —
          @endif
        </td>
        <td>{{ $c->hours ?? '—' }}</td>
        <td>
          @if(!empty($c->revoked_at))
            <span class="badge text-bg-danger">Revoked</span>
          @else
            <span class="badge text-bg-success">Issued</span>
          @endif
        </td>
        <td class="text-nowrap">
          @if(!empty($c->code))
            <a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ url('/qr/verify/'.$c->code) }}">Verify</a>
          @endif
          @if(!empty($c->pdf_url))
            <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ $c->pdf_url }}">PDF</a>
          @elseif(!empty($c->code))
            <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ url('/certificates/'.$c->code.'.pdf') }}">PDF</a>
          @endif
          @if(!empty($c->code))
            <a class="btn btn-sm btn-outline-secondary" target="_blank"
               href="https://wa.me/?text={{ urlencode(($c->title ?? 'Certificate').' — '.url('/qr/verify/'.$c->code)) }}">Share</a>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  </div>
  @endif
</div></section>
@endsection
