<?php

namespace App\Http\Requests;

use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LegalCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // route par auth:sanctum + role:superadmin
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'aadhaar_number' => preg_replace('/\D/', '', (string) $this->aadhaar_number),
            'pan_number'     => $this->filled('pan_number') ? strtoupper(preg_replace('/\s/', '', $this->pan_number)) : null,
            'client_phone'   => preg_replace('/\D/', '', (string) $this->client_phone),
            'advocate_id'    => $this->filled('advocate_id') ? $this->advocate_id : null,
        ]);
    }

    public function rules(): array
    {
        /** @var LegalCase|null $case */
        $case = $this->route('legalCase');
        $docs = 'mimes:pdf,doc,docx,jpg,jpeg,png,webp';

        return [
            // ---- case ----
            'title'             => ['required', 'string', 'min:3', 'max:200'],
            'practice_area_id'  => ['required', 'integer', Rule::exists('practice_areas', 'id')],
            'advocate_id'       => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_ADMIN)->where('status', 'active')],
            'status'            => ['required', Rule::in(array_keys(LegalCase::STATUSES))],
            'next_hearing_date' => ['nullable', 'date'],

            // ---- client ----
            'client_name'       => ['required', 'string', 'min:3', 'max:120'],
            'client_phone'      => ['required', 'regex:/^[6-9]\d{9}$/'],

            // ---- Aadhaar (mandatory) ----
            'aadhaar_number'    => ['required', 'regex:/^[2-9]\d{11}$/'],
            'aadhaar_file'      => [Rule::requiredIf(fn () => ! $case || ! $case->aadhaar_file), 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'],

            // ---- PAN (optional) ----
            'pan_number'        => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'pan_file'          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'],
            'remove_pan_file'   => ['nullable', 'boolean'],

            // ---- Documents (multiple) ----
            'documents'         => ['nullable', 'array', 'max:10'],
            'documents.*'       => ['file', $docs, 'max:10240'],
            'document_titles'   => ['nullable', 'array'],
            'document_titles.*' => ['nullable', 'string', 'max:150'],

            // ---- extra ----
            'client_email'      => ['nullable', 'email', 'max:150'],
            'court_name'        => ['nullable', 'string', 'max:150'],
            'opponent_name'     => ['nullable', 'string', 'max:150'],
            'filing_date'       => ['nullable', 'date', 'before_or_equal:today'],
            'priority'          => ['nullable', Rule::in(array_keys(LegalCase::PRIORITIES))],
            'description'       => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'            => 'Please enter the case title / matter.',
            'practice_area_id.required' => 'Please select a practice area.',
            'practice_area_id.exists'   => 'Selected practice area no longer exists.',
            'advocate_id.exists'        => 'Selected advocate is not active.',
            'client_name.required'      => 'Please enter the client name.',
            'client_phone.required'     => 'Please enter the client mobile number.',
            'client_phone.regex'        => 'Enter a valid 10-digit Indian mobile number.',
            'aadhaar_number.required'   => 'Aadhaar number is mandatory.',
            'aadhaar_number.regex'      => 'Enter a valid 12-digit Aadhaar number.',
            'aadhaar_file.required'     => 'Please upload the Aadhaar card (mandatory).',
            'aadhaar_file.mimes'        => 'Aadhaar card must be a PDF, JPG, PNG or WEBP file.',
            'aadhaar_file.max'          => 'Aadhaar card file must be smaller than 4MB.',
            'pan_number.regex'          => 'Enter a valid PAN number, e.g. ABCDE1234F.',
            'pan_file.max'              => 'PAN card file must be smaller than 4MB.',
            'documents.max'             => 'You can upload up to 10 documents at a time.',
            'documents.*.mimes'         => 'Documents must be PDF, DOC, DOCX, JPG, PNG or WEBP.',
            'documents.*.max'           => 'Each document must be smaller than 10MB.',
            'filing_date.before_or_equal' => 'Filing date cannot be in the future.',
        ];
    }
}