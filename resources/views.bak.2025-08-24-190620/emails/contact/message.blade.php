@component('mail::message')
**From:** {{ $data['name'] }} <{{ $data['email'] }}>

{{ $data['message'] }}
@endcomponent
