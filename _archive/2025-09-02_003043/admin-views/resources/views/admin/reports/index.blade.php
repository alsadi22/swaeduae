@extends('admin.layout')
@section('admin')
<div class="container py-3">
  <h1 class="h4 mb-3">Reports</h1>

  <form class="row g-2 mb-3" method="GET">
    <div class="col-auto"><input type="date" class="form-control form-control-sm" name="from" value="{{ $from }}"></div>
    <div class="col-auto"><input type="date" class="form-control form-control-sm" name="to"   value="{{ $to }}"></div>
    <div class="col-auto"><button class="btn btn-sm btn-primary">Apply</button></div>
    <div class="col-auto"><a class="btn btn-sm btn-outline-dark" href="{{ route('admin.reports.export',['table'=>'hours','from'=>$from,'to'=>$to]) }}">Export Hours</a></div>
    <div class="col-auto"><a class="btn btn-sm btn-outline-dark" href="{{ route('admin.reports.export',['table'=>'applications','from'=>$from,'to'=>$to]) }}">Export Applications</a></div>
    <div class="col-auto"><a class="btn btn-sm btn-outline-dark" href="{{ route('admin.reports.export',['table'=>'certificates','from'=>$from,'to'=>$to]) }}">Export Certificates</a></div>
  </form>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card"><div class="card-header">Hours by Opportunity</div>
      <div class="card-body p-0">
        <table class="table mb-0"><thead><tr><th>Opportunity</th><th>Total hours</th></tr></thead>
        <tbody>
        @forelse($data['hours_by_opportunity'] as $r)
          <tr><td>{{ $r->opportunity_id }}</td><td>{{ (float)$r->total }}</td></tr>
        @empty <tr><td colspan="2" class="text-muted">No data</td></tr> @endforelse
        </tbody></table>
      </div></div>
    </div>

    <div class="col-lg-6">
      <div class="card"><div class="card-header">Applications by Status</div>
      <div class="card-body p-0">
        <table class="table mb-0"><thead><tr><th>Status</th><th>Count</th></tr></thead>
        <tbody>
        @forelse($data['apps_by_status'] as $r)
          <tr><td>{{ $r->status }}</td><td>{{ $r->c }}</td></tr>
        @empty <tr><td colspan="2" class="text-muted">No data</td></tr> @endforelse
        </tbody></table>
      </div></div>
    </div>
  </div>
</div>
@endsection
