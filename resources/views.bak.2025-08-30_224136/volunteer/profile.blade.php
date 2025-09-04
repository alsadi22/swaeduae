@extends('layouts.app')
@section('title', __('Volunteer Dashboard'))

@section('content')
<div class="container py-4">

  {{-- HERO with avatar, name, email, and quick actions --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row align-items-center gy-3">
        <div class="col-12 col-md-7">
          @include('volunteer.partials.avatar_form', ['user' => $user ?? auth()->user()])
        </div>
        <div class="col-12 col-md-5 text-md-end">
          <div class="d-inline-flex flex-wrap gap-2">
            <a href="{{ url('/') }}" class="btn btn-light">{{ __('Back to home') }}</a>
            @if (Route::has('transcript.pdf'))
              <a href="{{ route('transcript.pdf') }}" class="btn btn-primary">{{ __('Download Transcript') }}</a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    {{-- LEFT: Sidebar --}}
    <aside class="col-12 col-lg-3">
      @include('volunteer.partials.sidebar')
    </aside>

    {{-- RIGHT: KPIs and lists --}}
    <section class="col-12 col-lg-9">

      <h2 class="h5 mb-3">{{ __('Volunteer Dashboard') }}</h2>

      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
              <div class="text-muted small">{{ __('ACCOUNT') }}</div>
              <div class="fw-semibold">{{ $user->name ?? '—' }}</div>
              <div class="small text-muted text-truncate">{{ $user->email ?? '—' }}</div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
              <div class="text-muted small">{{ __('TOTAL HOURS') }}</div>
              <div class="display-6 fw-bold">{{ number_format($totalHours ?? 0) }}</div>
              <div class="text-muted small">hrs</div>
              @if (Route::has('my.hours'))
                <div class="mt-2">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('my.hours') }}">{{ __('My Hours') }}</a>
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
              <div class="text-muted small">{{ __('UPCOMING OPPORTUNITIES') }}</div>
              <div class="display-6 fw-bold">{{ number_format($upcomingCount ?? 0) }}</div>
              <div class="mt-2">
                <a class="btn btn-sm btn-outline-primary" href="{{ url('/opportunities') }}">{{ __('Browse Opportunities') }}</a>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
              <div class="text-muted small">{{ __('CERTIFICATES') }}</div>
              <div class="display-6 fw-bold">{{ number_format($certCount ?? 0) }}</div>
              <div class="mt-2">
                <a class="btn btn-sm btn-outline-primary" href="{{ Route::has('my.certificates') ? route('my.certificates') : url('/my/certificates') }}">{{ __('My Certificates') }}</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      @if(($upcoming ?? collect())->count())
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-white"><strong>{{ __('Upcoming in the next 30 days') }}</strong></div>
          <div class="list-group list-group-flush">
            @foreach($upcoming as $o)
              <a href="{{ url('/opportunities/'.$o->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-medium">{{ $o->title }}</div>
                  <div class="small text-muted">
                    {{ \Carbon\Carbon::parse($o->start_date)->format('M j, Y') }}
                    @if(!empty($o->city)) • {{ $o->city }} @elseif(!empty($o->region)) • {{ $o->region }} @endif
                  </div>
                </div>
                <span class="btn btn-sm btn-outline-primary">{{ __('View') }}</span>
              </a>
            @endforeach
          </div>
        </div>
      @endif

      @if(($latestCerts ?? collect())->count())
        <div class="card shadow-sm">
          <div class="card-header bg-white"><strong>{{ __('Latest Certificates') }}</strong></div>
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="small text-muted">
                <tr>
                  <th>{{ __('Code') }}</th>
                  <th>{{ __('Opportunity') }}</th>
                  <th class="text-end">{{ __('Hours') }}</th>
                  <th class="text-end">{{ __('Actions') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($latestCerts as $c)
                  <tr>
                    <td>{{ $c->code ?? ('#'.$c->id) }}</td>
                    <td>{{ $c->opportunity_id }}</td>
                    <td class="text-end">{{ (int)($c->hours ?? 0) }}</td>
                    <td class="text-end">
                      @if (Route::has('certificates.show') && isset($c->code))
                        <a class="btn btn-sm btn-light" href="{{ route('certificates.show',$c->code) }}">{{ __('View') }}</a>
                      @endif
                      @if (Route::has('certificates.verify') && isset($c->code))
                        <a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('certificates.verify',$c->code) }}">{{ __('Verify') }}</a>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

    </section>
  </div>
</div>
@endsection
