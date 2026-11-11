<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Superadmin -> Create / Edit Admin
 * Fields bilkul Sign Up form jaise (users + user_profiles)
 *
 *   POST /api/admins            (create – password required)
 *   POST /api/admins/{admin}    (update – password optional)
 */
class AdminSignupRequest extends FormRequest
{
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
        $id       = $this->route('admin')?->id;
        $isUpdate = (bool) $id;

        return [
            // Personal
            'photo'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'name'       => ['required', 'string', 'min:3', 'max:100'],
            'gender'     => ['required', Rule::in(config('advocate.genders'))],
            'dob'        => ['required', 'date', 'before:-18 years', 'after:1920-01-01'],
            'occupation' => ['nullable', 'string', 'max:100'],

            // Contact & address
            'email'         => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($id)],
            'phone'         => ['required', 'digits:10', Rule::unique('users', 'phone')->ignore($id)],
            'alt_phone'     => ['nullable', 'digits:10', 'different:phone'],
            'address'       => ['required', 'string', 'max:255'],
            'city'          => ['required', 'string', 'max:80'],
            'state'         => ['required', Rule::in(config('advocate.states'))],
            'pincode'       => ['required', 'digits:6'],
            'interested_in' => ['nullable', Rule::in(array_column(config('practice', []), 'title'))],
            'heard_from'    => ['nullable', Rule::in(UserRegisterRequest::HEARD_FROM)],

            // Account
            'password' => [$isUpdate ? 'nullable' : 'required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'status'   => ['nullable', Rule::in(config('advocate.statuses'))],
        ];
    }

    public function messages(): array
    {
        return [
            'dob.before'          => 'Admin must be at least 18 years old.',
            'email.unique'        => 'This email is already registered.',
            'phone.unique'        => 'This mobile number is already registered.',
            'phone.digits'        => 'Enter a valid 10-digit mobile number.',
            'alt_phone.digits'    => 'Enter a valid 10-digit mobile number.',
            'alt_phone.different' => 'Alternate number must be different from the mobile number.',
            'pincode.digits'      => 'Pincode must be 6 digits.',
        ];
    }

    public function attributes(): array
    {
        return ['dob' => 'date of birth', 'alt_phone' => 'alternate mobile', 'interested_in' => 'practice area'];
    }
}