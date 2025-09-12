@extends("public.layout-travelpro")
<section class="text-center" style="padding:6rem 1rem;background:linear-gradient(180deg,#f8fafc,#eef2f7)">
  <div class="container">
    <h1 class="display-5 fw-bold mb-3">{{ __("Find Volunteer Opportunities in the UAE") }}</h1>
    <p class="lead mb-4">{{ __("Join events, track your hours, and earn verified certificates.") }}</p>
    <a href="/opportunities" class="btn btn-primary btn-lg me-2">{{ __("Explore Opportunities") }}</a>
    <a href="/events" class="btn btn-outline-secondary btn-lg">{{ __("Browse Events") }}</a>
  </div>
</section>
