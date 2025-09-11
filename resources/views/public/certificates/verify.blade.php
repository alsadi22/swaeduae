@extends('public.layout')
@section('title','Verify Certificate')
@section('content')
  <h1 class="mb-3">Verify Certificate</h1>
  <form method="GET" action="{{ route('certificates.verify.form') }}" class="mb-4" style="display:flex;gap:8px;align-items:center">
    <input type="text" name="code" placeholder="Enter code" value="{{ request('code') }}" required class="btn" style="width:280px">
    <button class="btn btn-primary">Verify</button>
  </form>
  @isset($certificate)
    <div class="card" style="border:1px solid #e5e7eb;border-radius:8px;padding:12px">
      <strong>Valid</strong> — {{ $certificate->holder_name }} · {{ $certificate->hours }}h · #{{ $certificate->code }}
    </div>
  @elseif(request('code'))
    <div class="card" style="border:1px solid #e5e7eb;border-radius:8px;padding:12px">
      <strong>Not found</strong> for code “{{ request('code') }}”
    </div>
  @endisset
@endsection
