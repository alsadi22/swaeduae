@extends('org.layout')
@section('title', __('Application Under Review'))

@section('content')
<div class="container text-center py-5">
    <h1 class="mb-4 text-success">{{ __('Thank you for registering!') }}</h1>
    <p>{{ __('Your organization’s application is now under review by our team.') }}</p>
    <p>{{ __('You will receive an email once it has been approved or rejected.') }}</p>
</div>
@endsection
