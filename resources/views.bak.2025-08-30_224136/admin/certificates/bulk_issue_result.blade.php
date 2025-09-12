@extends('admin.layout')
@section('title','Bulk Issue Result')

@section('content')
<div class="container-fluid py-3">
  <h1 class="mb-3">Bulk Issue Result – {{ $opp->title }}</h1>
  <div class="alert alert-info">
    Created: {{ $results['created'] }} · Updated: {{ $results['updated'] }} · Skipped: {{ $results['skipped'] }} · Errors: {{ $results['errors'] }}
  </div>
  @if(!empty($results['list']))
    <pre class="p-3 bg-light border">{{ implode("\n", $results['list']) }}</pre>
  @endif
  <a class="btn btn-secondary" href="{{ url('/admin/certificates/bulk-issue') }}">Back</a>
</div>
@endsection
