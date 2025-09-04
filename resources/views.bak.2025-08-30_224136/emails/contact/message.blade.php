@component('mail::message')
# New contact form message

**Name:** {{ $payload['name'] }}  
**Email:** {{ $payload['email'] }}  
**Subject:** {{ $payload['subject'] }}

**Message:**

{{ $payload['message'] }}

@endcomponent
