@component('mail::message')
# {{ $data['subject'] ?? __('Contact Message') }}

**From:** {{ $data['name'] ?? '' }} — {{ $data['email'] ?? '' }}

---

{{ $data['message'] ?? '' }}

@endcomponent
