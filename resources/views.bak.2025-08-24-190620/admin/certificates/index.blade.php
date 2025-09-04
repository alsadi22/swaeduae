@extends('admin.layout')
@section('title', __('Certificates'))

@section('content')
<div class="container-fluid py-4">
  <h1 class="mb-3" style="font-weight:700;">{{ __('Certificates') }}</h1>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

  <form class="card p-3 mb-3" method="GET" action="{{ url('/admin/certificates') }}">
    <div class="row g-2">
      <div class="col-sm-4"><input name="q" class="form-control" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Code, user, email, opportunity') }}"></div>
      <div class="col-sm-3">
        <select name="status" class="form-select">
          <option value="">{{ __('All') }}</option>
          <option value="valid" @selected(($filters['status'] ?? '')==='valid')>{{ __('Valid') }}</option>
          <option value="revoked" @selected(($filters['status'] ?? '')==='revoked')>{{ __('Revoked') }}</option>
        </select>
      </div>
      <div class="col-sm-3"><button class="btn btn-primary w-100" style="background:#9CAFAA;border-color:#9CAFAA;">{{ __('Filter') }}</button></div>
    </div>
  </form>

  <div class="card p-3 mb-3">
    <form method="POST" action="{{ url('/admin/opportunities/generate-certs') }}" class="row g-2">
      @csrf
      <div class="col-sm-4">
        <input type="number" name="opportunity_id" class="form-control" placeholder="{{ __('Opportunity ID') }}" required>
      </div>
      <div class="col-sm-3">
        <button class="btn btn-success w-100">{{ __('Generate for Opportunity') }}</button>
      </div>
      <div class="col-sm-5 text-muted small d-flex align-items-center">{{ __('Generates certificates for all volunteers with approved hours for the selected opportunity.') }}</div>
    </form>
  </div>

  <div class="table-responsive card">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th><th>{{ __('Code') }}</th><th>{{ __('User') }}</th><th>{{ __('Opportunity') }}</th><th>{{ __('Status') }}</th><th>{{ __('Created') }}</th><th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
      @forelse($rows as $c)
        <tr>
          <td>{{ $c->id }}</td>
          <td><a href="{{ url('/verify/'.$c->code) }}" target="_blank">{{ $c->code }}</a></td>
          <td>{{ $c->user_name }} <div class="small text-muted">{{ $c->email }}</div></td>
          <td>{{ $c->opportunity_title ?? '-' }}</td>
          <td>
            @if($c->revoked_at)
              <span class="badge bg-danger">{{ __('Revoked') }}</span>
            @else
              <span class="badge bg-success">{{ __('Valid') }}</span>
            @endif
          </td>
          <td class="text-muted">{{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}</td>
          <td class="d-flex flex-wrap gap-1">
            <a class="btn btn-sm btn-outline-primary" href="{{ url('/admin/certificates/'.$c->id) }}">{{ __('View') }}</a>
            <form method="POST" action="{{ url('/admin/certificates/'.$c->id.'/resend-email') }}">@csrf<button class="btn btn-sm btn-primary">{{ __('Resend Email') }}</button></form>
            <a class="btn btn-sm btn-outline-success" href="{{ url('/admin/certificates/'.$c->id.'/whatsapp') }}" target="_blank">{{ __('WhatsApp') }}</a>
            @if(!$c->revoked_at)
              <form method="POST" action="{{ url('/admin/certificates/'.$c->id.'/revoke') }}">@csrf<button class="btn btn-sm btn-danger">{{ __('Revoke') }}</button></form>
            @endif
            <form method="POST" action="{{ url('/admin/certificates/'.$c->id.'/reissue') }}">@csrf<button class="btn btn-sm btn-warning">{{ __('Reissue') }}</button></form>
          </td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-muted p-4">{{ __('No certificates yet.') }}</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">{{ $rows->links() }}</div>
</div>
@endsection
