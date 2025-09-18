@props([
    'name' => null,
    'value' => 1,
    'checked' => false,
    'id' => null,
])
<div class="form-check">
    <input type="checkbox"
           name="{{ $name }}"
           id="{{ $id ?? $name }}"
           value="{{ $value }}"
           class="form-check-input"
           {{ $checked ? 'checked' : '' }}
           {{ $attributes }}>
    @if($name)
        <label class="form-check-label" for="{{ $id ?? $name }}">{{ ucfirst(str_replace('_',' ', $name)) }}</label>
    @endif
</div>
