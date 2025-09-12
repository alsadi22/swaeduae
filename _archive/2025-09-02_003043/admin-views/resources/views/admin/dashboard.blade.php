@extends('admin.layout')

@section('admin')
<!-- ADMIN_KPI_OK -->
<div class="page-header min-height-200 border-radius-xl mt-3" style="background-image: url('/vendor/argon/assets/img/curved-images/curved14.jpg'); background-position: top;">
  <span class="mask bg-gradient-primary opacity-6"></span>
</div>

<div class="container-fluid py-4">

  <h1 class="h5 mb-3">Admin Dashboard</h1>

  {{-- KPI Cards --}}
  <div class="row">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Opportunities</p>
                <h5 class="font-weight-bolder mb-1">{{ $kpis['opportunities'] ?? 0 }}</h5>
                <a href="{{ url('/admin/opportunities') }}" class="text-primary text-sm">Manage</a>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-collection text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Events</p>
                <h5 class="font-weight-bolder mb-1">{{ $kpis['events'] ?? 0 }}</h5>
                <span class="text-sm text-muted">{{ $recent['events'] ?? 0 }} recent</span>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                <i class="ni ni-calendar-grid-58 text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Applications (pending)</p>
                <h5 class="font-weight-bolder mb-1">{{ $kpis['applications_pending'] ?? 0 }}</h5>
                <a href="{{ url('/admin/applicants') }}" class="text-primary text-sm">Review</a>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                <i class="ni ni-single-copy-04 text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Hours (pending)</p>
                <h5 class="font-weight-bolder mb-1">{{ $kpis['hours_pending'] ?? 0 }}</h5>
                <a href="{{ url('/admin/hours') }}" class="text-primary text-sm">Approve</a>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                <i class="ni ni-watch-time text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Second row --}}
  <div class="row mt-4">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <p class="text-sm mb-0 text-capitalize font-weight-bold">Certificates</p>
          <h5 class="font-weight-bolder mb-1">{{ $kpis['certificates'] ?? 0 }}</h5>
          <a href="{{ url('/admin/certificates') }}" class="text-primary text-sm">Open</a>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-body p-3">
          <p class="text-sm mb-0 text-capitalize font-weight-bold">Orgs (pending)</p>
          <h5 class="font-weight-bolder mb-1">{{ $kpis['orgs_pending'] ?? 0 }}</h5>
          <a href="{{ url('/admin/organizations') }}" class="text-primary text-sm">Approve</a>
        </div>
      </div>
    </div>

    <div class="col-xl-6">
      <div class="card">
        <div class="card-header pb-0"><h6>Overview</h6></div>
        <div class="card-body"><canvas id="chart-line" height="110"></canvas></div>
      </div>
    </div>
  </div>

  {{-- Quick actions --}}
  <div class="mt-4 d-flex gap-2 flex-wrap">
    <a class="btn btn-primary btn-sm" href="{{ url('/admin/opportunities') }}">Opportunities</a>
    <a class="btn btn-outline-secondary btn-sm" href="{{ url('/admin/applicants') }}">Applicants</a>
    <a class="btn btn-outline-secondary btn-sm" href="{{ url('/admin/attendance') }}">QR / Attendance</a>
    <a class="btn btn-outline-secondary btn-sm" href="{{ url('/admin/reports') }}">Reports</a>
    <a class="btn btn-outline-secondary btn-sm" href="{{ url('/admin/settings') }}">Settings</a>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  if (window.Chart) {
    var ctx = document.getElementById('chart-line').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: { labels: ['May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{ label:'Activity', data:[4,9,7,10,6,12,8,14], tension:.4, borderWidth:3 }] },
      options: { plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
    });
  }
});
</script>
@endpush
