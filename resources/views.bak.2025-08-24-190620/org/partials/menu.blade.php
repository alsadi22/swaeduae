@php
  $items = [
    ['label' => __('Dashboard'),      'href' => url('org/dashboard'),     'pat' => 'org/dashboard*'],
    ['label' => __('Opportunities'),  'href' => url('org/opportunities'), 'pat' => 'org/opportunities*'],
    ['label' => __('Applicants'),     'href' => url('org/applicants'),    'pat' => 'org/applicants*'],
    ['label' => __('Shortlist'),      'href' => url('org/shortlist'),     'pat' => 'org/shortlist*'],
    // Attendance scan is per-opportunity; link users to Opportunities to open QR there
    ['label' => __('Team'),           'href' => url('org/team'),          'pat' => 'org/team*'],
    ['label' => __('KYC / License'),  'href' => url('org/kyc'),           'pat' => 'org/kyc*'],
    ['label' => __('Settings'),       'href' => url('org/settings'),      'pat' => 'org/settings*'],
  ];
@endphp

<div class="org-subnav bg-light border-bottom">
  <div class="container py-2 d-flex flex-wrap gap-2 align-items-center">
    @foreach($items as $it)
      @php $active = request()->is($it['pat']) ? 'active' : ''; @endphp
      <a href="{{ $it['href'] }}" class="btn btn-sm btn-outline-secondary {{ $active }}" role="button">
        {{ $it['label'] }}
      </a>
    @endforeach

    <div class="ms-auto d-flex gap-2">
      <a href="{{ url('org/emails/preview?type=approved') }}" target="_blank" class="btn btn-sm btn-outline-primary">
        {{ __('Email Preview') }}
      </a>
    </div>
  </div>
</div>

<style>
  .org-subnav .btn.active{
    background: var(--org-primary, #0d6efd) !important;
    border-color: var(--org-primary, #0d6efd) !important;
    color: #fff !important;
  }
</style>
