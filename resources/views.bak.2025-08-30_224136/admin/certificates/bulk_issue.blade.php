@extends('admin.layout')
@section('title','Bulk Issue Certificates')

@section('content')
<div class="container-fluid py-3">
  <h1 class="mb-3">Bulk Issue Certificates</h1>
  <form method="post" action="{{ url('/admin/certificates/bulk-issue') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Opportunity</label>
      <select name="opportunity_id" class="form-select" required>
        <option value="">{{ __('Select...') }}</option>
        @foreach($opps as $o)
          <option value="{{ $o->id }}">{{ $o->title }} (ID: {{ $o->id }})</option>
        @endforeach
      </select>
    </div>
    <button class="btn btn-primary">Issue All</button>
  </form>
</div>
@endsection
