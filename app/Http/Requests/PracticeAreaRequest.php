<?php

namespace App\Http\Requests;

use App\Models\PracticeArea;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PracticeAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:120',
                Rule::unique('practice_areas', 'name')
                    ->ignore($this->route('practiceArea')?->id),
            ],

            'type' => [
                'required',
                'string',
                'min:2',
                'max:60',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:140',
                'alpha_dash',
                Rule::unique('practice_areas', 'slug')
                    ->ignore($this->route('practiceArea')?->id),
            ],

            'icon' => [
                'nullable',
                'string',
                'max:60',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'remove_image' => [
                'nullable',
                'boolean',
            ],

            'short_description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],

            'status' => [
                'nullable',
                Rule::in(PracticeArea::STATUSES),
            ],
        ];
    }
}