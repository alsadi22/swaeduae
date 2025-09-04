@include('org.settings._profile_form')
@extends('org.layout')
@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('Settings') }}</h1>
  @include('org.settings._branding_form')
</div>
@endsection

@include('org.settings._branding_form')
