@extends('org.layout')
@section('title', __('Organization Profile'))

@section('content')
<div class="container py-4">
  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <div class="mb-3">
            @if(($org?->logo_path))
              <img src="{{ $org?->logo_path }}" class="img-fluid rounded" style="max-height:120px" alt="logo">
            @else
              <div class="bg-light rounded p-5 text-muted">{{ __('No Logo') }}</div>
            @endif
          </div>
          <h5 class="mb-1">{{ $org?->name_en ?? auth()->user()->name }}</h5>
          <div class="text-muted">{{ $org?->name_ar }}</div>
          <div class="small mt-2">{{ $org?->emirate }} @if($org?->org_type) • {{ $org?->org_type }} @endif</div>
          <hr>
          <div class="text-start small">
            <div><strong>{{ __('License Status') }}:</strong> {{ $org?->license_status ?? 'none' }}</div>
            <div><strong>{{ __('Mobile') }}:</strong> {{ $org?->mobile }}</div>
            <div><strong>{{ __('Website') }}:</strong>
              @if($org?->website)<a href="{{ $org?->website }}" target="_blank">{{ $org?->website }}</a>@endif
            </div>
            <div><strong>{{ __('Public Email') }}:</strong> {{ $org?->public_email }}</div>
          </div>
          <form class="mt-3" method="POST" action="{{ route('org.license.request') }}">
            @csrf
            <button class="btn btn-outline-primary w-100" @disabled($org?->wants_license)>
              {{ ($org?->wants_license) ? __('License Requested') : __('Request Volunteer Licensing') }}
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
          <h5 class="mb-0">{{ __('Edit Organization Profile') }}</h5>
        </div>
        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
          @endif

          <form method="POST" action="{{ route('org.profile.update') }}" enctype="multipart/form-data" class="row g-3">
            @csrf

            <div class="col-md-6">
              <label class="form-label">{{ __('Organization Name (English)') }} *</label>
              <input type="text" name="name_en" class="form-control" required value="{{ old('name_en', $org?->name_en) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Organization Name (Arabic)') }}</label>
              <input type="text" name="name_ar" class="form-control" dir="rtl" value="{{ old('name_ar', $org?->name_ar) }}">
            </div>

            <div class="col-md-4">
              <label class="form-label">{{ __('Emirate') }} *</label>
              @php $ems = ['Abu Dhabi','Dubai','Sharjah','Ajman','Umm Al Quwain','Ras Al Khaimah','Fujairah']; @endphp
              <select name="emirate" class="form-select" required>
                @foreach($ems as $e)
                  <option value="{{ $e }}" @selected(old('emirate', $org?->emirate) === $e)>{{ $e }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ __('Type / Nature of Work') }} *</label>
              @php $types = ['Educational','Cultural','Charitable','Sports','Professional/Specialized','Environmental','Health','Community']; @endphp
              <select name="org_type" class="form-select" required>
                @foreach($types as $t)
                  <option value="{{ $t }}" @selected(old('org_type', $org?->org_type) === $t)>{{ $t }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ __('Logo') }}</label>
              <input type="file" name="logo" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">{{ __('Mobile Number') }} *</label>
              <input type="text" name="mobile" class="form-control" required value="{{ old('mobile', $org?->mobile) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ __('Website') }}</label>
              <input type="url" name="website" class="form-control" value="{{ old('website', $org?->website) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ __('Public Email') }}</label>
              <input type="email" name="public_email" class="form-control" value="{{ old('public_email', $org?->public_email) }}">
            </div>

            <div class="col-md-12">
              <label class="form-label">{{ __('Physical Address') }}</label>
              <input type="text" name="address" class="form-control" value="{{ old('address', $org?->address) }}">
            </div>

            <div class="col-md-12">
              <label class="form-label">{{ __('Description') }}</label>
              <textarea name="description" class="form-control" rows="3">{{ old('description', $org?->description) }}</textarea>
            </div>
            <div class="col-md-12">
              <label class="form-label">{{ __('Volunteer Programs / Activities') }}</label>
              <textarea name="volunteer_programs" class="form-control" rows="2">{{ old('volunteer_programs', $org?->volunteer_programs) }}</textarea>
            </div>

            <h6 class="mt-2">{{ __('Contact Person') }}</h6>
            <div class="col-md-4">
              <label class="form-label">{{ __('Name') }} *</label>
              <input type="text" name="contact_person_name" class="form-control" required value="{{ old('contact_person_name', $org?->contact_person_name) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ __('Email') }} *</label>
              <input type="email" name="contact_person_email" class="form-control" required value="{{ old('contact_person_email', $org?->contact_person_email) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ __('Phone') }} *</label>
              <input type="text" name="contact_person_phone" class="form-control" required value="{{ old('contact_person_phone', $org?->contact_person_phone) }}">
            </div>

            <div class="col-12 form-check mt-2">
              <input type="checkbox" class="form-check-input" id="wants_license" name="wants_license" value="1" @checked(old('wants_license', $org?->wants_license))>
              <label class="form-check-label" for="wants_license">{{ __('Request Volunteer Licensing Application') }}</label>
            </div>

            <div class="col-12 mt-3">
              <button class="btn btn-primary px-4" type="submit">{{ __('Save Changes') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
