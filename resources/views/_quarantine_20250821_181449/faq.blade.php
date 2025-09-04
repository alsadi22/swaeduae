@extends('layouts.app')

@section('title', __('FAQ'))
@section('content')
@include('partials.page_header', ['title' => __('Frequently Asked Questions'), 'subtitle' => __('Quick answers to common questions.')])

<div class="container my-4">
  <div class="accordion" id="faqAccordion">
    <div class="accordion-item">
      <h2 class="accordion-header" id="q1">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#a1">
          {{ __('How do I apply to an opportunity?') }}
        </button>
      </h2>
      <div id="a1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          {{ __('Open the opportunity page and click Apply. You can cancel before the event starts.') }}
        </div>
      </div>
    </div>
    <div class="accordion-item mt-2">
      <h2 class="accordion-header" id="q2">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">
          {{ __('How are my hours tracked?') }}
        </button>
      </h2>
      <div id="a2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          {{ __('Scan the QR to check in and out. Hours are computed from your attendance minutes.') }}
        </div>
      </div>
    </div>
    <div class="accordion-item mt-2">
      <h2 class="accordion-header" id="q3">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a3">
          {{ __('How do certificates work?') }}
        </button>
      </h2>
      <div id="a3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          {{ __('After completion and approval, we generate a PDF with a QR code. Anyone can verify it on the /verify page.') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
