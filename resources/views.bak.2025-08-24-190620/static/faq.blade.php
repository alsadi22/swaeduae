@extends('layouts.app')
@section('title', __('FAQ'))
@section('content')
<section id="main" tabindex="-1" class="container py-4">
  <h1 class="mb-3">{{ __('Frequently Asked Questions') }}</h1>

  <details class="mb-2">
    <summary>{{ __('How do I register?') }}</summary>
    <p>{{ __('Create an account, then browse opportunities and apply.') }}</p>
  </details>

  <details class="mb-2">
    <summary>{{ __('Is it free?') }}</summary>
    <p>{{ __('Yes—volunteering is free for individuals and organizations to join.') }}</p>
  </details>
</section>
@endsection
