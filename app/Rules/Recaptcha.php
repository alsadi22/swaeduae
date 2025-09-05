<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.recaptcha.secret') ?: env('RECAPTCHA_SECRET');
        // If not configured, skip (non-prod/dev won't break)
        if (!$secret) { return; }

        if (empty($value)) {
            $fail(__('Please complete the reCAPTCHA challenge.'));
            return;
        }

        $res = Http::asForm()->timeout(8)->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret'   => $secret,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]
        );

        if (!($res->ok() && (bool) data_get($res->json(), 'success'))) {
            $fail(__('reCAPTCHA verification failed. Please try again.'));
        }
    }
}
