<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public form
    }

    public function rules(): array
    {
        return [
            'name'    => ['required','string','max:120'],
            'email'   => ['required','email','max:190'],
            'subject' => ['required','string','max:150'],
            'message' => ['required','string','max:5000'],
            // anti-bot
            'hp_field' => ['nullable','prohibited'], // honeypot must stay empty
            'hp_time'  => ['nullable','integer','min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'hp_field.prohibited' => 'Spam detected.',
        ];
    }
}
