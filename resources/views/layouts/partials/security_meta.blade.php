<meta name="csrf-token" content="{{ csrf_token() }}">
@production
    {{-- CSP is enforced via headers in production --}}
@else
    <meta http-equiv="Content-Security-Policy"
          content="default-src 'self' data:; img-src 'self' data:;
                   style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline';
                   font-src 'self' data:; connect-src 'self';
                   frame-ancestors 'self'; upgrade-insecure-requests">
@endproduction
<meta name="referrer" content="strict-origin-when-cross-origin">
{{-- Hook for page-specific meta --}}
@yield('meta')
