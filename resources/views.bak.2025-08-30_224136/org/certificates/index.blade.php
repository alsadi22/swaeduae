@extends('org.layout')
@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('Certificates') }} — {{ $opportunity->title ?? ('#'.$opportunity->id) }}</h1>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
      <div class="card shadow-sm"><div class="card-body">
        <div class="text-muted small">{{ __('Already Issued') }}</div>
        <div class="h3 mb-0">{{ $issuedCount }}</div>
      </div></div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card shadow-sm"><div class="card-body">
        <div class="text-muted small">{{ __('Eligible (Approved)') }}</div>
        <div class="h3 mb-0">{{ $eligibleApproved }}</div>
      </div></div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card shadow-sm"><div class="card-body">
        <div class="text-muted small">{{ __('Eligible (Attended)') }}</div>
        <div class="h3 mb-0">{{ $eligibleAttended }}</div>
      </div></div>
    </div>
  </div>

  <div class="d-flex gap-2 flex-wrap mb-3">
    <form method="POST" action="{{ route('org.certificates.issue', $opportunity) }}">
      @csrf
      <input type="hidden" name="mode" value="approved">
      <button class="btn btn-primary">{{ __('Issue for Approved') }}</button>
    </form>
    <form method="POST" action="{{ route('org.certificates.issue', $opportunity) }}">
      @csrf
      <input type="hidden" name="mode" value="attended">
      <button class="btn btn-outline-primary">{{ __('Issue for Attended') }}</button>
    </form>
    <a class="btn btn-outline-secondary" href="{{ route('org.certificates.export.csv', $opportunity) }}">{{ __('Export CSV') }}</a>
  </div>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      <p class="text-muted mb-2">{{ __('To resend emails, paste certificate IDs (comma separated)') }}</p>
      <form method="POST" action="{{ route('org.certificates.resend', $opportunity) }}" class="d-flex flex-wrap gap-2">
        @csrf
        <input type="text" name="certificate_ids[]" class="form-control" placeholder="1,2,3" oninput="syncIds(this)">
        <button class="btn btn-secondary">{{ __('Resend Emails') }}</button>
      </form>
    </div>
  </div>
</div>
<script>
function syncIds(input){
  const form = input.closest('form');
  // reset other hidden inputs
  form.querySelectorAll('input[type=hidden][name="certificate_ids[]"]').forEach(x=>x.remove());
  const raw = input.value || '';
  raw.split(',').map(s=>s.trim()).filter(Boolean).forEach(id=>{
    const h=document.createElement('input');h.type='hidden';h.name='certificate_ids[]';h.value=id;form.appendChild(h);
  });
}
</script>
@endsection
