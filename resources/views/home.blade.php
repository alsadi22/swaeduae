@extends('public.layout-travelpro')
@section('title','Welcome to SwaedUAE')
@section('meta_description','Volunteer opportunities and community projects in the UAE.')

@section('content')
<section class="tp-hero section">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-8">
        <h1 class="display-5 fw-bold mb-3">Welcome to <span class="text-primary">SwaedUAE</span></h1>
        <p class="lead text-muted mb-4">Find, join, and track meaningful volunteer opportunities across the UAE.</p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="{{ url('/opportunities') }}" class="btn btn-primary">Explore Opportunities</a>
          <a href="{{ url('/about') }}" class="btn btn-outline-primary">Learn more</a>
          <a href="{{ url('/my/certificates') }}" class="btn btn-outline-secondary">My Certificates</a>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-3">
      <div class="col-md-4"><div class="feature-card">
        <h5>Easy Check-in</h5><p class="text-muted mb-0">QR attendance with geofence & full audit trail.</p>
      </div></div>
      <div class="col-md-4"><div class="feature-card">
        <h5>Instant Certificates</h5><p class="text-muted mb-0">Auto-issued PDFs with public verification.</p>
      </div></div>
      <div class="col-md-4"><div class="feature-card">
        <h5>Bilingual</h5><p class="text-muted mb-0">English & Arabic ready from day one.</p>
      </div></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="h4 mb-0">Featured Opportunities</h2>
      <a href="{{ url('/opportunities') }}" class="small">View all</a>
    </div>
    <div class="op-grid">
      @php $ops = [
        ['title'=>'Beach Cleanup','org'=>'Green Shores','city'=>'Dubai','date'=>'This Friday','hours'=>'2–3 hours'],
        ['title'=>'Food Drive','org'=>'Helping Hands','city'=>'Sharjah','date'=>'Next Week','hours'=>'3–5 hours'],
        ['title'=>'Book Donation Sorting','org'=>'Read&Rise','city'=>'Abu Dhabi','date'=>'This Weekend','hours'=>'2 hours'],
      ]; @endphp
      @foreach($ops as $o)
        <x-opportunity-card :title="$o['title']" :org="$o['org']" :city="$o['city']" :date="$o['date']" :hours="$o['hours']" />
      @endforeach
    </div>
  </div>
</section>
@endsection
