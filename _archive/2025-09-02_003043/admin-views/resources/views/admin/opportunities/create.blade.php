@extends('admin.layout')
@section('title', __('New Opportunity'))
@section('page_title', __('New Opportunity'))
@section('content')
<div class="row">
  <div class="col-12 col-lg-10 mx-auto">
    <div class="card shadow border-0">
      <div class="card-header pb-0"><h6 class="mb-0">{{ __('Details') }}</h6></div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.opportunities.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">{{ __('Title') }}</label>
            <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror">
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Start Date') }}</label>
            <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control @error('start_date') is-invalid @enderror">
            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Region') }}</label>
            <input type="text" name="region" value="{{ old('region') }}" class="form-control @error('region') is-invalid @enderror">
            @error('region')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">{{ __('Create') }}</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.opportunities.index') }}">{{ __('Cancel') }}</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
