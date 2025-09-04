@extends(view()->exists('layouts.org') ? 'layouts.org' : (view()->exists('layouts.app') ? 'layouts.app' : null))

@section('content')
<div class="container py-3">
  <h1 class="h4 mb-3">{{ __('Shortlist') }}</h1>
  <div class="alert alert-info">
    {{ __('Shortlist page is up. You can now add the table / bulk actions here.') }}
  </div>
  @isset($opportunity)
    <p class="text-muted mb-0">{{ __('Opportunity ID:') }} {{ is_object($opportunity) ? $opportunity->id : $opportunity }}</p>
  @endisset
</div>
@endsection

@include('org.shortlist._counters', ['opportunity' => $opportunity ?? null, 'opportunity_id' => $opportunity_id ?? null])

@include('org.shortlist._slotcap_form', ['opportunity' => $opportunity ?? null, 'opportunity_id' => $opportunity_id ?? null])
