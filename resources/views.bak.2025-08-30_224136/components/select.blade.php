@props([
    'name' => null,
    'options' => [],
    'value' => null,
    'placeholder' => null,
])
<select name="{{ $name }}" {{ $attributes->merge(['class' => 'form-select']) }}>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    @foreach($options as $key => $label)
        <option value="{{ $key }}" @selected(old($name, $value) == $key)>{{ $label }}</option>
    @endforeach
</select>
