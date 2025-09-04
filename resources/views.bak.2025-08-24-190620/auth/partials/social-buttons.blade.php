{{-- Social & UAEPass buttons (always visible; disable if route missing) --}}
@php
    use Illuminate\Support\Facades\Route;
    $hasGoogle  = Route::has('oauth.google.redirect') || Route::has('login.google') || Route::has('social.google');
    $hasUAEPass = Route::has('uaepass.redirect')      || Route::has('login.uaepass') || Route::has('social.uaepass');
    $googleRoute  = Route::has('oauth.google.redirect') ? 'oauth.google.redirect' : (Route::has('login.google') ? 'login.google' : (Route::has('social.google') ? 'social.google' : null));
    $uaepassRoute = Route::has('uaepass.redirect')      ? 'uaepass.redirect'      : (Route::has('login.uaepass') ? 'login.uaepass' : (Route::has('social.uaepass') ? 'social.uaepass' : null));
@endphp

<div class="mt-4 grid gap-3">
    @if($uaepassRoute)
        <a href="{{ route($uaepassRoute) }}"
           class="btn btn-outline w-full"
           style="display:block;padding:.65rem 1rem;border:1px solid #cfd3dc;border-radius:10px;text-align:center;">
            Continue with UAEPass
        </a>
    @else
        <button type="button"
                class="btn btn-outline w-full opacity-60 cursor-not-allowed"
                style="display:block;padding:.65rem 1rem;border:1px dashed #cfd3dc;border-radius:10px;text-align:center;opacity:.6;cursor:not-allowed;"
                disabled
                title="UAEPass coming soon">
            Continue with UAEPass
        </button>
    @endif

    @if($googleRoute)
        <a href="{{ route($googleRoute) }}"
           class="btn btn-outline w-full"
           style="display:block;padding:.65rem 1rem;border:1px solid #cfd3dc;border-radius:10px;text-align:center;">
            Continue with Google
        </a>
    @else
        <button type="button"
                class="btn btn-outline w-full opacity-60 cursor-not-allowed"
                style="display:block;padding:.65rem 1rem;border:1px dashed #cfd3dc;border-radius:10px;text-align:center;opacity:.6;cursor:not-allowed;"
                disabled
                title="Google login coming soon">
            Continue with Google
        </button>
    @endif
</div>
