<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * POST /api/auth/user-register  (multipart/form-data)
 * Website user (role = user) ka poora registration
 */
class UserRegisterRequest extends FormRequest
{
    public const HEARD_FROM = ['Google Search', 'Friend / Family', 'Social Media', 'Advertisement', 'Advocate Referral', 'Other'];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $clean = fn ($v) => $v ? substr(preg_replace('/\D/', '', (string) $v), -10) : $v;

        $this->merge([
            'email'     => $this->email ? strtolower(trim($this->email)) : $this->email,
            'phone'     => $clean($this->phone),
            'alt_phone' => $clean($this->alt_phone),
        ]);
    }

    public function rules(): array
    {
        return [
            // Personal
            'photo'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'name'       => ['required', 'string', 'min:3', 'max:100'],
            'gender'     => ['required', Rule::in(config('advocate.genders'))],
            'dob'        => ['required', 'date', 'before:-18 years', 'after:1920-01-01'],
            'occupation' => ['nullable', 'string', 'max:100'],

            // Contact & address
            'email'         => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'phone'         => ['required', 'digits:10', Rule::unique('users', 'phone')],
            'alt_phone'     => ['nullable', 'digits:10', 'different:phone'],
            'address'       => ['required', 'string', 'max:255'],
            'city'          => ['required', 'string', 'max:80'],
            'state'         => ['required', Rule::in(config('advocate.states'))],
            'pincode'       => ['required', 'digits:6'],
            'interested_in' => ['nullable', Rule::in(array_column(config('practice', []), 'title'))],
            'heard_from'    => ['nullable', Rule::in(self::HEARD_FROM)],

            // Account
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'terms'    => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'dob.before'         => 'You must be at least 18 years old to register.',
            'email.unique'       => 'This email is already registered. Please log in.',
            'phone.unique'       => 'This mobile number is already registered.',
            'phone.digits'       => 'Enter a valid 10-digit mobile number.',
            'alt_phone.digits'   => 'Enter a valid 10-digit mobile number.',
            'alt_phone.different'=> 'Alternate number must be different from the mobile number.',
            'pincode.digits'     => 'Pincode must be 6 digits.',
            'terms.accepted'     => 'Please accept the terms & privacy policy.',
        ];
    }

    public function attributes(): array
    {
        return ['dob' => 'date of birth', 'alt_phone' => 'alternate mobile', 'interested_in' => 'practice area'];
    }
}