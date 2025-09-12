@extends(public.layout)
<section style="padding:3rem 1rem;max-width:1100px;margin:4rem auto;">
  <h1 style="font-weight:800;margin-bottom:1rem">{{ __('Find Volunteer Opportunities in the UAE') }}</h1>
  <p style="margin-bottom:1.25rem">{{ __('Join events, track your hours, and earn verified certificates.') }}</p>
  <div>
    <a href="{{ url('/opportunities') }}" style="display:inline-block;padding:.6rem 1rem;border-radius:.5rem;background:#5562ea;color:#fff;text-decoration:none;margin-right:.5rem">{{ __('Explore Opportunities') }}</a>
    <a href="{{ url('/events') }}" style="display:inline-block;padding:.6rem 1rem;border-radius:.5rem;border:1px solid #c9ced6;text-decoration:none">{{ __('Browse Events') }}</a>
  </div>
</section>
