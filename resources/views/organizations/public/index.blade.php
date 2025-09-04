@extends('layouts.app')
@section('title', __('Organizations'))
@section('content')
<div class="container py-4">
  <h1 class="mb-3">{{ __('Organizations') }}</h1>

  @isset($message)
    <div class="alert alert-warning">{{ $message }}</div>
  @endisset

  <form method="GET" action="{{ route('orgs.public.index') }}" class="card p-3 mb-3">
    <div class="row g-2">
      <div class="col-md-6">
        <input name="q" class="form-control" placeholder="{{ __('Search by name or text') }}" value="{{ $filters['q'] ?? '' }}">
      </div>
      <div class="col-md-4">
        <select name="emirate" class="form-select">
          <option value="">{{ __('All emirates') }}</option>
          @foreach(($emirates ?? []) as $e)
            <option value="{{ $e }}" @selected(($filters['emirate'] ?? '')===$e)>{{ $e }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-primary">{{ __('Filter') }}</button>
      </div>
    </div>
  </form>

  @if(($rows->total() ?? 0) === 0)
    <div class="alert alert-info">{{ __('No organizations found.') }}</div>
  @endif

  <div class="row">
    @foreach($rows as $o)
      @php
        $title = $o->name ?? ($o->title ?? __('Organization'));
        $slug  = \Illuminate\Support\Str::slug($title);
      @endphp
      <div class="col-md-6 mb-3">
        <a class="card h-100 p-3 text-decoration-none" href="{{ route('orgs.public.show', [$o->id, $slug]) }}">
          <h5 class="mb-1">{{ $title }}</h5>
          <div class="text-muted">
            @php $e = $o->emirate ?? ($o->region ?? ($o->city ?? null)); @endphp
            {{ $e ?? '' }}
          </div>
        </a>
      </div>
    @endforeach
  </div>

  <div class="mt-3">{{ $rows->links() }}</div>
</div>
@endsection
