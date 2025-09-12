@extends('admin.layout')
@section('title','Integrations')
@section('content')
  <h1 class="mb-3">Integrations</h1>
  <form method="POST" action="{{ route('admin.integrations.save') }}" class="card p-3">
    @csrf
    <div class="row g-4">
      @php $providers=['google'=>'Google','apple'=>'Apple','facebook'=>'Facebook','uaepass'=>'UAE PASS (OIDC, placeholder)']; @endphp
      @foreach($providers as $key=>$label)
      <div class="col-12">
        <div class="border rounded p-3 bg-white">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">{{ $label }}</h5>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="{{ $key }}_enabled" name="{{ $key }}_enabled" {{ ($values[$key]['enabled']??false)?'checked':'' }}>
              <label class="form-check-label" for="{{ $key }}_enabled">Enabled</label>
            </div>
          </div>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Client ID</label>
              <input class="form-control" name="{{ $key }}_client_id" value="{{ $values[$key]['client_id'] ?? '' }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Client Secret</label>
              <input class="form-control" name="{{ $key }}_client_secret" value="{{ $values[$key]['client_secret'] ?? '' }}">
            </div>
            <div class="col-md-12">
              <label class="form-label">Redirect URL</label>
              <input class="form-control" name="{{ $key }}_redirect" value="{{ $values[$key]['redirect'] ?? '' }}">
            </div>
          </div>
        </div>
      </div>
      @endforeach

      <div class="col-12">
        <div class="border rounded p-3 bg-white">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">Stripe Payments</h5>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="stripe_enabled" name="stripe_enabled" {{ ($values['stripe']['enabled']??false)?'checked':'' }}>
              <label class="form-check-label" for="stripe_enabled">Enabled</label>
            </div>
          </div>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Publishable Key</label>
              <input class="form-control" name="stripe_key" value="{{ $values['stripe']['key'] ?? '' }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Secret Key</label>
              <input class="form-control" name="stripe_secret" value="{{ $values['stripe']['secret'] ?? '' }}">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Save</button></div>
  </form>
@endsection
