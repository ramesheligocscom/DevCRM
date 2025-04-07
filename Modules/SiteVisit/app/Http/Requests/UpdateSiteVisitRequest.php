<?php

namespace Modules\SiteVisit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiteVisitRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'visit_time' => [
                'sometimes',
                'date',
                'after_or_equal:today'
            ],
            'visit_assignee' => [
                'sometimes',
                'exists:users,id',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('is_active', true);
                })
            ],
            'status' => [
                'sometimes',
                'string',
                'max:255',
                Rule::in(['scheduled', 'completed', 'canceled', 'rescheduled'])
            ],
            'visit_notes' => [
                'nullable',
                'string',
                'max:2000'
            ],
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
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'visit_time.after_or_equal' => 'The visit time must be today or in the future.',
            'visit_assignee.exists' => 'The selected assignee is invalid or not an active user.',
            'status.in' => 'The status must be one of: scheduled, completed, canceled, or rescheduled.',
            'lead_id.required' => 'Either lead_id or client_id is required.',
            'client_id.required' => 'Either lead_id or client_id is required.',
            'visit_notes.max' => 'Visit notes cannot exceed 2000 characters.'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert empty strings to null for nullable fields
        $this->merge([
            'visit_notes' => $this->visit_notes ?: null,
            'lead_id' => $this->lead_id ?: null,
            'client_id' => $this->client_id ?: null
        ]);
    }
}
