@extends('layouts.app')
@section('title', __('Calendar'))
@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <a class="btn btn-outline-secondary" href="{{ url('/calendar') }}?y={{ $prev->year }}&m={{ $prev->month }}">← {{ $prev->format('M Y') }}</a>
    <h1 class="mb-0">{{ $month->format('F Y') }}</h1>
    <a class="btn btn-outline-secondary" href="{{ url('/calendar') }}?y={{ $next->year }}&m={{ $next->month }}">{{ $next->format('M Y') }} →</a>
  </div>

  <div class="mb-3">
    <a class="btn btn-sm btn-primary" href="{{ url('/calendar.ics') }}">{{ __('Subscribe (.ics)') }}</a>
  </div>

  @php
    $start = $month->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
    $end   = $month->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
    $map = [];
    foreach ($items as $it) {
      $key = \Carbon\Carbon::parse($it->start)->format('Y-m-d');
      $map[$key] = $map[$key] ?? [];
      $map[$key][] = $it;
    }
  @endphp

  <div class="table-responsive">
    <table class="table table-bordered">
      <thead><tr>
        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)<th>{{ __($d) }}</th>@endforeach
      </tr></thead>
      <tbody>
        @for($cur = $start->copy(); $cur <= $end; $cur->addDay())
          @if($cur->dayOfWeek === 0)<tr>@endif
            @php $k = $cur->format('Y-m-d'); @endphp
            <td class="{{ $cur->month !== $month->month ? 'bg-light' : '' }}" style="vertical-align:top;">
              <div class="small text-muted">{{ $cur->day }}</div>
              @if(!empty($map[$k]))
                @foreach($map[$k] as $e)
                  @php
                    $url = $e->table === 'events'
                      ? url('/events/'.$e->id)
                      : route('public.opportunity.show', ['id'=>$e->id]);
                  @endphp
                  <div class="mt-1">
                    <a href="{{ $url }}">{{ \Illuminate\Support\Str::limit($e->title, 42) }}</a>
                  </div>
                @endforeach
              @endif
            </td>
          @if($cur->dayOfWeek === 6)</tr>@endif
        @endfor
      </tbody>
    </table>
  </div>
</div>
@endsection
