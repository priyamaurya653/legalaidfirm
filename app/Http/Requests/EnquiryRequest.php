<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Contact page form (public – bina login) */
class EnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'    => trim((string) $this->name),
            'email'   => strtolower(trim((string) $this->email)),
            'phone'   => substr(preg_replace('/\D/', '', (string) $this->phone), -10),
            'subject' => $this->filled('subject') ? (string) $this->subject : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'min:3', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'phone'   => ['required', 'regex:/^[6-9]\d{9}$/'],
            // practice area id  ya  "other"
            'subject' => ['nullable', function ($attr, $value, $fail) {
                if ($value !== 'other' && ! \App\Models\PracticeArea::active()->whereKey($value)->exists()) {
                    $fail('Please select a valid practice area.');
                }
            }],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'website' => ['nullable', 'max:0'],      // honeypot (bots ke liye) – khaali hona chahiye
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Please enter your full name.',
            'email.required'   => 'Please enter your email address.',
            'email.email'      => 'Please enter a valid email address.',
            'phone.required'   => 'Please enter your phone number.',
            'phone.regex'      => 'Please enter a valid 10-digit mobile number.',
            'message.required' => 'Please describe your legal matter.',
            'message.min'      => 'Please describe your matter in at least 10 characters.',
            'website.max'      => 'Spam detected.',
        ];
    }
}