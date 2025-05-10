<?php

namespace Modules\SiteVisit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteVisitStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in(['scheduled', 'completed', 'canceled', 'rescheduled'])
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be one of: scheduled, completed, canceled, or rescheduled.',
        ];
    }
} 
