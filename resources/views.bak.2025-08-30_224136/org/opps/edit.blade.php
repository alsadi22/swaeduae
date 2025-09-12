@extends('org.layout')
@section('title', __('Edit Opportunity').' | '.config('app.name'))

@section('page_header')
  <x-page-header :title="__('Edit Opportunity')" />
@endsection

@section('content')
  @include('partials.flash')

  <form method="POST" action="{{ route('org.opportunities.update', $op ?? $opportunity) }}">
    @csrf
    @include('org.opps._form', ['op' => $op ?? $opportunity])
  </form>
@endsection
