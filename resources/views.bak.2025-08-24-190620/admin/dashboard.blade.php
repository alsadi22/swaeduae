@extends('admin.layout')

@section('title', __('Admin Dashboard'))
@section('page_title', __('Dashboard'))

@section('content')
<div class="row g-4">
  <!-- Stat cards -->
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card shadow border-0">
      <div class="card-body">
        <p class="text-sm mb-1 text-secondary">{{ __('Total Volunteers') }}</p>
        <h4 class="mb-0">{{ number_format($totalVolunteers ?? 0) }}</h4>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card shadow border-0">
      <div class="card-body">
        <p class="text-sm mb-1 text-secondary">{{ __('Organizations') }}</p>
        <h4 class="mb-0">{{ number_format($totalOrganizations ?? 0) }}</h4>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card shadow border-0">
      <div class="card-body">
        <p class="text-sm mb-1 text-secondary">{{ __('Opportunities') }}</p>
        <h4 class="mb-0">{{ number_format($totalOpportunities ?? 0) }}</h4>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card shadow border-0">
      <div class="card-body">
        <p class="text-sm mb-1 text-secondary">{{ __('Hours Logged') }}</p>
        <h4 class="mb-0">{{ number_format(($totalHours ?? 0), 0) }}</h4>
      </div>
    </div>
  </div>

  <!-- Recent opportunities table -->
  <div class="col-12">
    <div class="card shadow border-0">
      <div class="card-header pb-0">
        <h6 class="mb-0">{{ __('Recent Opportunities') }}</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Title') }}</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Org') }}</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Start') }}</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse(($recentOpps ?? []) as $op)
              <tr>
                <td class="text-sm">{{ $op->title }}</td>
                <td class="text-sm">{{ $op->organization->name ?? '—' }}</td>
                <td class="text-sm">{{ optional($op->start_date)->format('Y-m-d') ?? '—' }}</td>
                <td class="text-end">
                  <a href="{{ route('admin.opportunities.edit', $op->id) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                </td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-secondary p-4">— {{ __('No data yet') }} —</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer text-end">
        <a href="{{ route('admin.opportunities.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('View all') }}</a>
      </div>
    </div>
  </div>
</div>
@endsection
