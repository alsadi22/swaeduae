@if(($type ?? request('type','volunteer')) === 'volunteer')
  @includeIf('auth.partials.social-buttons')
  @if(\Illuminate\Support\Facades\Route::has('uaepass.redirect'))
    <div class="d-grid mt-2">
      <a class="btn btn-outline-secondary" href="{{ route('uaepass.redirect') }}">{{ __('Continue with UAE PASS') }}</a>
    </div>
  @endif
@endif
