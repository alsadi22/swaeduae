@extends('admin.layout')

@section('content')
  <h3 class="mb-3">{{ __('Edit Opportunity') }}</h3>

  <form method="post" action="{{ route('admin.opportunities.update',$item->id) }}" class="card p-3">
    @csrf
    @method('PUT')

    @php
      use Illuminate\Support\Facades\Schema;

      // helper to pick the first existing column
      $pick = function(array $cands) {
        foreach ($cands as $c) if (Schema::hasColumn('opportunities',$c)) return $c;
        return null;
      };

      $colTitle    = $pick(['title','name']);
      $colLocation = $pick(['location','city','emirate','region']);
      $colStart    = $pick(['start_date','starts_at','date','from_date']);
      $colEnd      = $pick(['end_date','ends_at','to_date']);
      $colSeats    = $pick(['seats','capacity','max_volunteers']);
      $colDesc     = $pick(['description','body','details']);
      $hasSlug     = Schema::hasColumn('opportunities','slug');
    @endphp

    <div class="row g-3">
      @if($colTitle)
        <div class="col-md-8">
          <label class="form-label">{{ __('Title') }}</label>
          <input name="{{ $colTitle }}" class="form-control" value="{{ $item->{$colTitle} ?? '' }}">
        </div>
      @endif

      @if($hasSlug)
        <div class="col-md-4">
          <label class="form-label">{{ __('Slug') }}</label>
          <input name="slug" class="form-control" value="{{ $item->slug ?? '' }}">
        </div>
      @endif

      @if($colLocation)
        <div class="col-md-4">
          <label class="form-label">{{ __('Location') }}</label>
          <input name="{{ $colLocation }}" class="form-control" value="{{ $item->{$colLocation} ?? '' }}">
        </div>
      @endif

      @if($colStart)
        <div class="col-md-4">
          <label class="form-label">{{ __('Start Date') }}</label>
          <input type="date" name="{{ $colStart }}" class="form-control"
                 value="{{ \Illuminate\Support\Str::of($item->{$colStart} ?? '')->substr(0,10) }}">
        </div>
      @endif

      @if($colEnd)
        <div class="col-md-4">
          <label class="form-label">{{ __('End Date') }}</label>
          <input type="date" name="{{ $colEnd }}" class="form-control"
                 value="{{ \Illuminate\Support\Str::of($item->{$colEnd} ?? '')->substr(0,10) }}">
        </div>
      @endif

      @if($colSeats)
        <div class="col-md-4">
          <label class="form-label">{{ __('Seats') }}</label>
          <input type="number" name="{{ $colSeats }}" class="form-control" value="{{ $item->{$colSeats} ?? '' }}">
        </div>
      @endif

      @if($colDesc)
        <div class="col-12">
          <label class="form-label">{{ __('Description') }}</label>
          <textarea name="{{ $colDesc }}" class="form-control" rows="6">{{ $item->{$colDesc} ?? '' }}</textarea>
        </div>
      @endif
    </div>

    <div class="mt-3">
      <button class="btn btn-primary">{{ __('Update') }}</button>
    </div>
  </form>

  <form method="post" action="{{ route('admin.opportunities.destroy',$item->id) }}" class="mt-2"
        onsubmit="return confirm('{{ __('Delete this opportunity?') }}')">
    @csrf
    @method('DELETE')
    <button class="btn btn-outline-danger">{{ __('Delete') }}</button>
  </form>
@endsection
