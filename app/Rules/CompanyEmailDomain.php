<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CompanyEmailDomain implements ValidationRule
{
    /** @var array<string> */
    private array $blocked = [
        'gmail.com','googlemail.com','yahoo.com','yahoo.com.sa','outlook.com','hotmail.com','live.com',
        'icloud.com','aol.com','yandex.com','proton.me','protonmail.com','mail.com','gmx.com'
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !str_contains($value, '@')) {
            $fail('The :attribute must be a valid email.');
            return;
        }
        $domain = strtolower(substr(strrchr($value, '@'), 1));
        if (in_array($domain, $this->blocked, true)) {
            $fail('Please use a company email address (not a free webmail domain).');
        }
    }
}
