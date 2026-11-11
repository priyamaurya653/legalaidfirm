<?php

namespace App\Http\Requests;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // route par auth:sanctum + role:superadmin
    }

    protected function prepareForValidation(): void
    {
        $digits = fn ($v) => preg_replace('/\D/', '', (string) $v);

        $this->merge([
            'phone'          => $digits($this->phone),
            'alt_phone'      => $this->filled('alt_phone') ? $digits($this->alt_phone) : null,
            'aadhaar_number' => $digits($this->aadhaar_number),
            'pan_number'     => $this->filled('pan_number') ? strtoupper(preg_replace('/\s/', '', $this->pan_number)) : null,
            'email'          => $this->filled('email') ? strtolower(trim($this->email)) : null,
            'advocate_id'    => $this->filled('advocate_id') ? $this->advocate_id : null,
            'practice_area_id' => $this->filled('practice_area_id') ? $this->practice_area_id : null,
        ]);
    }

    public function rules(): array
    {
        /** @var Client|null $client */
        $client = $this->route('client');
        $kyc    = ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'];

        return [
            // ---- basic ----
            'name'    => ['required', 'string', 'min:3', 'max:120'],
            'phone'   => ['required', 'regex:/^[6-9]\d{9}$/', Rule::unique('clients', 'phone')->ignore($client?->id), Rule::unique('users', 'phone')->ignore($client?->user_id)],
            // ---- login (client = user) ----
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($client?->user_id)],
            'password' => [Rule::requiredIf(fn () => ! $client || ! $client->user_id), 'nullable', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'city'    => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:500'],

            // ---- case kya hai + kisko assign ----
            'practice_area_id' => ['required', 'integer', Rule::exists('practice_areas', 'id')],
            'case_details'     => ['nullable', 'string', 'max:3000'],
            // admin ya superadmin – dono ko assign kar sakte hain
            'advocate_id'      => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($q) => $q->whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPERADMIN])->where('status', 'active'))],

            // ---- KYC ----
            'aadhaar_number' => ['required', 'regex:/^[2-9]\d{11}$/'],
            'aadhaar_file'   => [Rule::requiredIf(fn () => ! $client || ! $client->aadhaar_file), 'nullable', ...$kyc],
            'pan_number'     => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'pan_file'       => ['nullable', ...$kyc],
            'remove_pan_file' => ['nullable', 'boolean'],

            // ---- documents ----
            'documents'         => ['nullable', 'array', 'max:10'],
            'documents.*'       => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp', 'max:10240'],
            'document_titles'   => ['nullable', 'array'],
            'document_titles.*' => ['nullable', 'string', 'max:150'],

            // ---- 4 extra ----
            'alt_phone' => ['nullable', 'regex:/^[6-9]\d{9}$/', 'different:phone'],
            'state'     => ['nullable', 'string', 'max:60'],
            'pincode'   => ['nullable', 'digits:6'],
            'status'    => ['nullable', Rule::in(array_keys(Client::STATUSES))],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Please enter the client name.',
            'phone.required'            => 'Please enter the client mobile number.',
            'phone.regex'               => 'Enter a valid 10-digit Indian mobile number.',
            'phone.unique'              => 'This mobile number is already registered.',
            'email.required'            => 'Email is required – the client logs in with it.',
            'email.unique'              => 'This email is already registered with another account.',
            'password.required'         => 'Please set a login password for the client.',
            'password.confirmed'        => 'Passwords do not match.',
            'practice_area_id.required' => 'Please select what this case is about.',
            'advocate_id.exists'        => 'Selected admin / superadmin is not active.',
            'aadhaar_number.required'   => 'Aadhaar number is mandatory.',
            'aadhaar_number.regex'      => 'Enter a valid 12-digit Aadhaar number.',
            'aadhaar_file.required'     => 'Please upload the Aadhaar card.',
            'aadhaar_file.max'          => 'Aadhaar card file must be smaller than 4MB.',
            'pan_number.regex'          => 'Enter a valid PAN number, e.g. ABCDE1234F.',
            'pan_file.max'              => 'PAN card file must be smaller than 4MB.',
            'documents.*.mimes'         => 'Documents must be PDF, DOC, DOCX, JPG, PNG or WEBP.',
            'documents.*.max'           => 'Each document must be smaller than 10MB.',
            'alt_phone.regex'           => 'Enter a valid 10-digit alternate number.',
            'alt_phone.different'       => 'Alternate number must be different from the mobile number.',
            'pincode.digits'            => 'Pincode must be 6 digits.',
        ];
    }
}