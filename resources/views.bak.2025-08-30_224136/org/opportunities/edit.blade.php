@extends('org.layout')
@section('title', __('Edit Opportunity').' | '.config('app.name'))
@section('page_header')
  <x-page-header :title="__('Edit Opportunity')" :subtitle="$opp->title">
    <x-slot name="actions">
      <form method="POST" class="d-inline" action="{{ route('org.opportunities.destroy',$opp) }}">@csrf @method('DELETE')
        <button class="btn btn-outline-danger" onclick="return confirm('{{ __('Delete?') }}')">{{ __('Delete') }}</button>
      </form>
      <a class="btn btn-outline-teal" href="{{ route('admin.attendance.qr',$opp) }}"><i class="bi bi-qr-code me-1"></i>{{ __('Show QR') }}</a>
      <form method="POST" class="d-inline" action="{{ route('certificates.issue',$opp) }}">@csrf
        <button class="btn btn-teal"><i class="bi bi-award me-1"></i>{{ __('Issue Certificates') }}</button>
      </form>
      <a class="btn btn-outline-secondary" href="{{ route('opportunities.attendances.csv', $opp) }}">{{ __('Export CSV') }}</a>
  </x-slot>
  </x-page-header>
@endsection
@section('content')
  <form method="POST" action="{{ route('org.opportunities.update',$opp) }}" class="card shadow-sm p-3">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">{{ __('Title') }}</label><input name="title" class="form-control" value="{{ $opp->title }}" required></div>
      <div class="col-md-3"><label class="form-label">{{ __('Category') }}</label><input name="category" class="form-control" value="{{ $opp->category }}"></div>
      <div class="col-md-3"><label class="form-label">{{ __('City') }}</label><input name="city" class="form-control" value="{{ $opp->city }}"></div>
      <div class="col-12"><label class="form-label">{{ __('Location') }}</label><input name="location" class="form-control" value="{{ $opp->location }}"></div>
      <div class="col-md-6"><label class="form-label">{{ __('Starts At') }}</label><input type="datetime-local" name="starts_at" class="form-control" value="{{ optional($opp->starts_at)->format('Y-m-d\TH:i') }}"></div>
      <div class="col-md-6"><label class="form-label">{{ __('Ends At') }}</label><input type="datetime-local" name="ends_at" class="form-control" value="{{ optional($opp->ends_at)->format('Y-m-d\TH:i') }}"></div>
      <div class="col-12"><label class="form-label">{{ __('Description') }}</label><textarea name="description" rows="6" class="form-control">{{ $opp->description }}</textarea></div>
      <div class="col-12 form-check ms-2"><input type="checkbox" class="form-check-input" id="completed" name="is_completed" value="1" {{ $opp->is_completed ? 'checked' : '' }}> <label class="form-check-label" for="completed">{{ __('Completed') }}</label></div>
    </div>
    <div class="mt-3"><button class="btn btn-teal">{{ __('Save') }}</button></div>
  </form>
@endsection
