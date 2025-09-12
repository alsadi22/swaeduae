@extends('layouts.app')
@section('title', __('Organizations'))
@section('content')
@include('partials.page_header', ['title' => __('Organizations')])

<div class="container my-4">
  <form method="GET" action="" class="row g-2 mb-3">
    <div class="col-md-6">
      <input name="q" class="form-control" placeholder="{{ __('Search organizations...') }}" value="{{ request('q') }}">
    </div>
    <div class="col-md-3">
      <select name="sort" class="form-select" onchange="this.form.submit()">
        <option value="name" {{ request('sort','name')==='name'?'selected':'' }}>{{ __('Name') }}</option>
        <option value="newest" {{ request('sort')==='newest'?'selected':'' }}>{{ __('Newest') }}</option>
      </select>
    </div>
    <div class="col-md-3">
      <button class="btn btn-primary w-100">{{ __('Apply') }}</button>
    </div>
  </form>

  @if($orgs->count() === 0)
    <div class="alert alert-info">{{ __('No organizations found.') }}</div>
  @endif

  <div class="row g-3">
    @foreach ($orgs as $org)
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <h5 class="card-title mb-2">{{ $org->name ?? __('Organization') }}</h5>
            @if(!empty($org->description))
              <p class="text-muted small mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($org->description), 140) }}</p>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-3">
    {{ $orgs->links() }}
  </div>
</div>
@endsection
