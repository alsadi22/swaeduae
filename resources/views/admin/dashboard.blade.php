@extends('layouts.admin')
@section('title','Dashboard')

@section('content')
@php
  use Illuminate\Support\Facades\Schema;
  use Illuminate\Support\Facades\DB;
  $has = fn($t)=> Schema::hasTable($t);
  $count = fn($t)=> $has($t)? DB::table($t)->count(): 0;

  $kpis = [
    ['lbl'=>'Users','num'=>$count('users'),'cls'=>'kpi-1'],
    ['lbl'=>'Organizations','num'=>$has('organizations')? DB::table('organizations')->whereNull('deleted_at')->count():0,'cls'=>'kpi-2'],
    ['lbl'=>'Opportunities','num'=>$count('opportunities'),'cls'=>'kpi-3'],
    ['lbl'=>'Certificates','num'=>$count('certificates'),'cls'=>'kpi-4'],
  ];
@endphp

<div class="row g-3 mb-3">
  @foreach($kpis as $k)
  <div class="col-12 col-md-6 col-lg-3">
    <div class="card card-kpi {{ $k['cls'] }}">
      <div class="card-body">
        <div class="lbl">{{ $k['lbl'] }}</div>
        <div class="num">{{ number_format($k['num']) }}</div>
        <div class="small">Total</div>
      </div>
    </div>
  </div>
  @endforeach
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Sales value</strong><span class="text-muted small">demo chart</span>
      </div>
      <div class="card-body"><canvas id="chartLine" height="130"></canvas></div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Total orders</strong><span class="text-muted small">demo chart</span>
      </div>
      <div class="card-body"><canvas id="chartBar" height="130"></canvas></div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Simple demo data; replace with real series later if you want
  const L = document.getElementById('chartLine');
  const B = document.getElementById('chartBar');

  if (L) new Chart(L, {
    type:'line',
    data:{ labels:['May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets:[{ label:'Value', data:[5,18,26,19,31,22,28,58], tension:.35, fill:false }]},
    options:{ plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
  });

  if (B) new Chart(B, {
    type:'bar',
    data:{ labels:['Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets:[{ label:'Orders', data:[24,18,30,22,15,29] }]},
    options:{ plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
  });
</script>
@endpush
@endsection
