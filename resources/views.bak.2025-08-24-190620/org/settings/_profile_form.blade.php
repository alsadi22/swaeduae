<form method="POST" action="{{ route('org.settings.update') }}" class="card shadow-sm mb-3">
  @csrf
  <div class="card-body row g-3">
    <div class="col-12 col-md-6">
      <label class="form-label">{{ __('Organization Name') }}</label>
      <input type="text" class="form-control" name="name" value="{{ old('name', $settings['name'] ?? '') }}">
    </div>
    <div class="col-12">
      <label class="form-label">{{ __('Address') }}</label>
      <textarea class="form-control" name="address" rows="2">{{ old('address', $settings['address'] ?? '') }}</textarea>
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">{{ __('Phone') }}</label>
      <input type="text" class="form-control" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}">
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">{{ __('Website') }}</label>
      <input type="url" class="form-control" name="website" value="{{ old('website', $settings['website'] ?? '') }}">
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Twitter</label>
      <input type="text" class="form-control" name="twitter" value="{{ old('twitter', $settings['twitter'] ?? '') }}">
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Facebook</label>
      <input type="text" class="form-control" name="facebook" value="{{ old('facebook', $settings['facebook'] ?? '') }}">
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Instagram</label>
      <input type="text" class="form-control" name="instagram" value="{{ old('instagram', $settings['instagram'] ?? '') }}">
    </div>
    <div class="col-12">
      <button class="btn btn-primary">{{ __('Save Profile') }}</button>
    </div>
  </div>
</form>
