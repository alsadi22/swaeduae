@extends('admin.layout')
@section('title', __('Admin Dashboard'))

@section('content')
<style>
  .kpi-card{background:#fff;border-radius:16px;padding:18px;box-shadow:0 8px 20px rgba(0,0,0,.06)}
  .kpi-value{font-size:2rem;font-weight:800;line-height:1}
  .kpi-label{color:#6b7280;font-size:.9rem}
  .section-card{background:#fff;border-radius:16px;padding:20px;box-shadow:0 8px 20px rgba(0,0,0,.06)}
  .table-sm td,.table-sm th{padding:.45rem .6rem}
  .muted{color:#6b7280}
  .quick-actions .btn{border-radius:999px}
  .chart-box{position:relative;height:280px}
  #hoursChart,#usersChart{position:absolute;inset:0;width:100%!important;height:100%!important}
</style>



<div class="container py-3">
  <div class="d-flex align-items-center mb-3">
    <h1 class="h3 fw-bold m-0">{{ __('Admin Dashboard / لوحة التحكم') }}</h1>
    <div class="ms-auto quick-actions d-none d-md-flex gap-2">
      <a class="btn btn-primary"
         href="{{ Route::has('admin.opportunities.create') ? (Route::has('admin.opportunities.create') ? route('admin.opportunities.create') : url('/admin/opportunities/create')) : url('/admin/opportunities/create') }}">
        {{ __('New Opportunity') }}
      </a>
      <a class="btn btn-outline-secondary"
         href="{{ Route::has('home') ? route('home') : url('/') }}">
      </a>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><div class="kpi-card text-center"><div class="kpi-label">{{ __('Users / المستخدمون') }}</div><div class="kpi-value">{{ number_format($users) }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card text-center"><div class="kpi-label">{{ __('Organizations / الجهات') }}</div><div class="kpi-value">{{ number_format($orgs) }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card text-center"><div class="kpi-label">{{ __('Opportunities / الفرص') }}</div><div class="kpi-value">{{ number_format($opps) }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card text-center"><div class="kpi-label">{{ __('Hours (sum) / إجمالي الساعات') }}</div><div class="kpi-value">{{ number_format($hours) }}</div></div></div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-lg-6"><div class="section-card"><h5 class="mb-3">{{ __('New Users (last 8 weeks)') }}</h5><div class="chart-box"><canvas id="usersChart"></canvas></div></div></div>
    <div class="col-12 col-lg-6"><div class="section-card"><h5 class="mb-3">{{ __('Weekly Volunteer Hours (last 8 weeks) / الساعات الأسبوعية') }}</h5><div class="chart-box"><canvas id="hoursChart"></canvas></div></div></div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-6">
      <div class="section-card">
        <h5 class="mb-2">{{ __('Recent Opportunities / أحدث الفرص') }}</h5>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light"><tr><th>{{ __('Title') }}</th><th>{{ __('Region') }}</th><th>{{ __('Created') }}</th></tr></thead>
            <tbody>
              @forelse($recentOpps as $o)
              <tr>
                <td><a href="{{ url('/admin/opportunities/'.($o->id ?? '').'/edit') }}">{{ $o->title ?? __('(untitled)') }}</a></td>
                <td class="muted">{{ $o->region }}</td>
                <td class="muted">{{ optional($o->created_at)->format('M d Y') }}</td>
              </tr>
              @empty
              <tr><td colspan="3" class="muted">{{ __('No opportunities yet.') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="section-card">
        <div class="d-flex align-items-center mb-2">
          <h5 class="m-0">{{ __('Recent Users / أحدث المستخدمين') }}</h5>
          <a class="ms-auto btn btn-sm btn-outline-secondary" href="{{ url('/admin/users') }}">{{ __('Manage users') }}</a>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light"><tr><th>{{ __('Name') }}</th><th>{{ __('Email') }}</th><th>{{ __('Joined') }}</th></tr></thead>
            <tbody>
              @forelse($recentUsers as $u)
              <tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td class="muted">{{ optional($u->created_at)->format('M d Y') }}</td></tr>
              @empty
              <tr><td colspan="3" class="muted">{{ __('No users yet.') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  const labels = @json($labels), hoursData = @json($weeklyHours), usersData = @json($weeklyUsers);
  const uc = document.getElementById('usersChart');
  if (uc) new Chart(uc.getContext('2d'), { type:'line', data:{ labels, datasets:[{ label:'Users', data:usersData, tension:.35, borderWidth:2, pointRadius:2 }]}, options:{ responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true}} }});
  const hc = document.getElementById('hoursChart');
  if (hc) new Chart(hc.getContext('2d'), { type:'bar', data:{ labels, datasets:[{ label:'Hours', data:hoursData, borderWidth:1 }]}, options:{ responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true}} }});
})();
</script>
@endsection
