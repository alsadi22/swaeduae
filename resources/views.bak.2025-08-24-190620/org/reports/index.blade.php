@extends('org.layout')

@section('title', __('Reports'))

@section('content')
<div class="container-fluid mt-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">{{ __('Volunteer Hours Report') }}</h1>
        <a href="{{ route('org.reports.hours.csv', request()->query()) }}" class="btn btn-primary">
            {{ __('Download CSV') }}
        </a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('org.reports') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('From') }}</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('To') }}</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Opportunity') }}</label>
                    <select name="opportunity_id" class="form-select">
                        <option value="">{{ __('All') }}</option>
                        @foreach($opportunities as $o)
                            <option value="{{ $o->id }}" @selected($oppId==$o->id)>{{ $o->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100">{{ __('Filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Preview (first 100 rows)') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Volunteer') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Opportunity') }}</th>
                            <th class="text-end">{{ __('Hours') }}</th>
                            <th class="text-end">{{ __('Minutes') }}</th>
                            <th class="text-end">{{ __('Entries') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            @php $hours = round(($r->total_minutes ?? 0)/60,2); @endphp
                            <tr>
                                <td>{{ $r->volunteer_name }}</td>
                                <td>{{ $r->volunteer_email }}</td>
                                <td>{{ $r->opportunity_title }}</td>
                                <td class="text-end">{{ number_format($hours,2) }}</td>
                                <td class="text-end">{{ (int)($r->total_minutes ?? 0) }}</td>
                                <td class="text-end">{{ (int)($r->entries ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted p-4">{{ __('No data for selected filters.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
