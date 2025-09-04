@extends("org.layout")


@if($oppId)
<form method="POST" action="{{ route('org.opportunities.slotcap.update', ['opportunity' => $oppId]) }}" class="card shadow-sm mb-3">
  @csrf
  <div class="card-body d-flex flex-wrap align-items-end gap-3">
    <div>
      <label class="form-label mb-1">{{ __('Slot Cap') }}</label>
      <input type="number" min="0" step="1" name="slot_cap" value="{{ old('slot_cap', $capValue) }}" class="form-control" style="max-width:140px">
      <div class="form-text">{{ __('Leave empty for no cap') }}</div>
    </div>
    <button class="btn btn-primary">{{ __('Save Cap') }}</button>
    @if (session('status'))
      <span class="text-success">{{ session('status') }}</span>
    @endif
    @error('slot_cap')
      <span class="text-danger">{{ $message }}</span>
    @enderror
  </div>
</form>
@endif
