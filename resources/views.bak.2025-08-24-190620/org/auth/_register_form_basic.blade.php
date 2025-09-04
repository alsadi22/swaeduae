@php(
  $orgRegisterAction = \Illuminate\Support\Facades\Route::has('org.register.store')
    ? route('org.register.store')
    : url('/org/register')
)
<form method="POST" action="{{ $orgRegisterAction }}" enctype="multipart/form-data" class="mx-auto" style="max-width:640px">
  @csrf
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">{{ __('Organization Name') }}</label>
      <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('Business Email') }}</label>
      <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('Password') }}</label>
      <input type="password" name="password" class="form-control" required autocomplete="new-password">
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('Confirm Password') }}</label>
      <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('Trade License Number') }}</label>
      <input type="text" name="trade_license_number" class="form-control" required value="{{ old('trade_license_number') }}">
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('Trade License (PDF/JPG/PNG)') }}</label>
      <input type="file" name="trade_license_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('Organization Logo') }}</label>
      <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png" required>
    </div>
  </div>
  <button type="submit" class="btn btn-success mt-3 w-100">{{ __('Create Organization Account') }}</button>
</form>
