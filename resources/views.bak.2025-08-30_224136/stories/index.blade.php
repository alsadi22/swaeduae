@extends('layouts.app')
@section('title','Stories | '.config('app.name'))
@section('page_header') <x-page-header :title="__('Volunteer Stories')" :subtitle="__('Impact from the community')" /> @endsection
@section('content')
  <div class="empty-state">{{ __('Coming soon.') }}</div>
@endsection
