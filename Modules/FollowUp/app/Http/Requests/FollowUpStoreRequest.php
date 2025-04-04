<?php

namespace Modules\FollowUp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FollowUpStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'call_status' => [
                'required',
                Rule::in(['completed', 'pending', 'no_answer', 'busy', 'failed'])
            ],
            'lead_prospect' => 'required|string|max:32',
            'call_summary' => 'nullable|string|max:2000',
            'lead_id' => [
                'nullable',
                'exists:leads,id',
                Rule::requiredIf(function () {
                    return empty($this->client_id);
                })
            ],
            'client_id' => [
                'nullable',
                'exists:clients,id',
                Rule::requiredIf(function () {
                    return empty($this->lead_id);
                })
            ]
        ];
    }

    public function messages()
    {
        return [
            'call_status.in' => 'Invalid call status. Must be: completed, pending, no_answer, busy, or failed.',
            'lead_prospect.max' => 'Lead prospect cannot exceed 32 characters.',
            'call_summary.max' => 'Call summary cannot exceed 2000 characters.',
            'lead_id.required' => 'Either lead_id or client_id is required.',
            'client_id.required' => 'Either lead_id or client_id is required.'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'created_by' => auth()->id()
        ]);
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
