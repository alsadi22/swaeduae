@extends('org.layout')
@section('title', __('New Opportunity').' | '.config('app.name'))
@section('page_header')
  <x-page-header :title="__('Create Opportunity')" :subtitle="__('Basic details')"/>
@endsection
@section('content')
  <form method="POST" action="{{ route('org.opps.store') }}" class="card shadow-sm p-3">
    @csrf
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">{{ __('Title') }}</label><input name="title" class="form-control" required></div>
      <div class="col-md-3"><label class="form-label">{{ __('Category') }}</label><input name="category" class="form-control"></div>
      <div class="col-md-3"><label class="form-label">{{ __('City') }}</label><input name="city" class="form-control"></div>
      <div class="col-12"><label class="form-label">{{ __('Location') }}</label><input name="location" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">{{ __('Starts At') }}</label><input type="datetime-local" name="starts_at" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">{{ __('Ends At') }}</label><input type="datetime-local" name="ends_at" class="form-control"></div>
      <div class="col-12"><label class="form-label">{{ __('Description') }}</label><textarea name="description" rows="6" class="form-control"></textarea></div>
    </div>
    <div class="mt-3"><button class="btn btn-teal">{{ __('Create') }}</button></div>
  </form>
@endsection
