@php($kpis=$kpis??['volunteers'=>0,'events'=>0,'hours'=>0,'applications'=>0])
@extends('org.layout') {{-- We reuse the Argon admin layout for consistency --}}

@section('title', __('Organization Dashboard'))

@section('content')
@include('org.dashboard._kpis')
<div class="container-fluid mt-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">{{ __('Organization Dashboard') }}</h1>
        <div>
            <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm">{{ __('View Site') }}</a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row">
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card card-stats shadow-sm h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-title text-uppercase text-muted mb-2">{{ __('Total Volunteers Hosted') }}</h6>
                            <span class="h2 font-weight-bold mb-0">{{ number_format($volunteersHosted) }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                <i class="ni ni-single-02"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm text-muted">{{ __('Distinct attendees across all events') }}</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card card-stats shadow-sm h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-title text-uppercase text-muted mb-2">{{ __('Total Hours Contributed') }}</h6>
                            <span class="h2 font-weight-bold mb-0">{{ number_format($totalHours, 2) }} <small class="text-muted">{{ __('hrs') }}</small></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                <i class="ni ni-time-alarm"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm text-muted">{{ __('Computed from volunteer hours') }}</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card card-stats shadow-sm h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-title text-uppercase text-muted mb-2">{{ __('Upcoming Opportunities') }}</h6>
                            <span class="h2 font-weight-bold mb-0">{{ number_format($upcomingOpps) }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                <i class="ni ni-calendar-grid-58"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm text-muted">{{ __('Active & future dated') }}</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card card-stats shadow-sm h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-title text-uppercase text-muted mb-2">{{ __('Certificates Issued') }}</h6>
                            <span class="h2 font-weight-bold mb-0">{{ number_format($certificatesIssued) }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                <i class="ni ni-paper-diploma"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm text-muted">{{ __('All time') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row (data-ready; renderer can be Chart.js later) --}}
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                    <h5 class="mb-0">{{ __('Volunteer Hours (Last 12 Months)') }}</h5>
                </div>
                <div class="card-body">
                    <div id="hoursChart"
                         data-labels='@json($monthLabels)'
                         data-series='@json($hoursSeries)'
                         class="w-100" style="height:260px;">
                        <noscript>{{ __('Chart requires JavaScript') }}</noscript>
                    </div>
                    <small class="text-muted">{{ __('Shows monthly total hours') }}</small>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                    <h5 class="mb-0">{{ __('Applications vs Attendance') }}</h5>
                </div>
                <div class="card-body">
                    <div id="appAttendChart"
                         data-labels='@json($appAttend["labels"])'
                         data-apps='@json($appAttend["apps"])'
                         data-attend='@json($appAttend["attend"])'
                         class="w-100" style="height:260px;">
                        <noscript>{{ __('Chart requires JavaScript') }}</noscript>
                    </div>
                    <small class="text-muted">{{ __('Last 10 opportunities') }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="mb-2">{{ __('Create New Opportunity') }}</h5>
                    <p class="text-muted mb-3">{{ __('Publish an opportunity and start accepting applications.') }}</p>
                    <div class="mt-auto">
                        <a href="{{ url('/org/opportunities/create') }}" class="btn btn-primary">{{ __('Create') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="mb-2">{{ __('Export Volunteer Hours') }}</h5>
                    <p class="text-muted mb-3">{{ __('Get a CSV of hours by event or date range.') }}</p>
                    <div class="mt-auto">
                        <a href="{{ url('/org/reports') }}" class="btn btn-outline-secondary">{{ __('Open Reports') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="mb-2">{{ __('Bulk Send Certificates') }}</h5>
                    <p class="text-muted mb-3">{{ __('Issue and send certificates for completed events.') }}</p>
                    <div class="mt-auto">
                        <a href="{{ url('/org/certificates/bulk') }}" class="btn btn-outline-info">{{ __('Open Certificates') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Minimal vanilla renderer (placeholder) to show bars/lines without external libs) --}}
    {{-- Recent Activity --}}
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0"><h5 class="mb-0">{{ __('Recent Activity') }}</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse(($recentActivity ?? []) as $it)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge badge-pill badge-secondary mr-2">{{ ucfirst(data_get($it,'type','?')) }}</span>
                                <a href="{{ route('org.events.volunteers', ['opportunity' => data_get($it,'opportunity_id')]) }}" class="text-decoration-none"><strong>{{ data_get($it,'who','?') }}</strong></a>
                                @if(data_get($it,'type')==='attendance')
                                    — {{ __('minutes') }}: {{ (int) data_get($it,'minutes',0) }}
                                @else
                                    — {{ __('status') }}: {{ data_get($it,'status','applied') }}
                                @endif
                            </div>
                            <small class="text-muted">
                                {{ \Illuminate\Support\Carbon::parse(data_get($it,'when'))->diffForHumans() }} · <a href="{{ route('org.events.volunteers', ['opportunity' => data_get($it,'opportunity_id')]) }}" class="ml-2">{{ __('View') }}</a>
                            </small>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">{{ __('No recent activity yet.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@push('scripts')
<script>
(function(){
    function simpleLine(el){
        try{
            const labels = JSON.parse(el.dataset.labels || '[]');
            const series = JSON.parse(el.dataset.series || '[]');
            if(!labels.length || !series.length) return;

            // simple SVG line chart
            const w = el.clientWidth || 600, h = 240, p=24;
            const max = Math.max(...series, 1);
            const stepX = (w - p*2)/(series.length-1 || 1);
            const scaleY = (h - p*2)/max;
            let d = '';
            series.forEach((v,i)=>{
                const x = p + i*stepX;
                const y = h - p - v*scaleY;
                d += (i===0 ? 'M' : 'L') + x + ' ' + y + ' ';
            });
            const ns='http://www.w3.org/2000/svg';
            const svg = document.createElementNS(ns,'svg');
            svg.setAttribute('width', w);
            svg.setAttribute('height', h);
            const path = document.createElementNS(ns,'path');
            path.setAttribute('d', d);
            path.setAttribute('fill','none');
            path.setAttribute('stroke','currentColor');
            path.setAttribute('stroke-width','2');
            svg.appendChild(path);
            el.innerHTML='';
            el.appendChild(svg);
        }catch(e){}
    }
    function simpleBars(el){
        try{
            const labels = JSON.parse(el.dataset.labels || '[]');
            const apps   = JSON.parse(el.dataset.apps || '[]');
            const attend = JSON.parse(el.dataset.attend || '[]');
            if(!labels.length) return;
            const w = el.clientWidth || 600, h = 240, p=24;
            const n = labels.length;
            const max = Math.max(1, ...apps, ...attend);
            const bw = (w - p*2) / (n*2); // two bars per group
            const scaleY = (h - p*2)/max;
            const ns='http://www.w3.org/2000/svg';
            const svg = document.createElementNS(ns,'svg');
            svg.setAttribute('width', w);
            svg.setAttribute('height', h);
            // draw bars: apps then attend
            for(let i=0;i<n;i++){
                const baseX = p + i*(bw*2) + i*4;
                const aH = apps[i]*scaleY, tH = attend[i]*scaleY;
                const aRect = document.createElementNS(ns,'rect');
                aRect.setAttribute('x', baseX);
                aRect.setAttribute('y', h - p - aH);
                aRect.setAttribute('width', bw);
                aRect.setAttribute('height', aH);
                aRect.setAttribute('fill', 'currentColor');
                aRect.setAttribute('opacity', '0.35');
                svg.appendChild(aRect);

                const tRect = document.createElementNS(ns,'rect');
                tRect.setAttribute('x', baseX + bw + 2);
                tRect.setAttribute('y', h - p - tH);
                tRect.setAttribute('width', bw);
                tRect.setAttribute('height', tH);
                tRect.setAttribute('fill', 'currentColor');
                tRect.setAttribute('opacity', '0.75');
                svg.appendChild(tRect);
            }
            el.innerHTML='';
            el.appendChild(svg);
        }catch(e){}
    }

    const lc = document.getElementById('hoursChart');
    const bc = document.getElementById('appAttendChart');
    if(lc) simpleLine(lc);
    if(bc) simpleBars(bc);
})();
</script>
@endpush

@endsection

@include('org.partials.dashboard_v1')

@include('org.partials.recent_activity')

@include('org.partials.certs_quick')

@include('org.partials.hours_chart')

@include('org.partials.upcoming_7d')

@include('org.partials.today_checkins')

@include('org.partials.trends')

@include('org.partials.top_volunteers')
