<?php

namespace Modules\SiteVisit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StoreSiteVisitRequest extends FormRequest
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
                'required',
                'date',
                'after_or_equal:now',
                'before_or_equal:' . now()->addMonths(3)
            ],
            'visit_assignee' => [
                'nullable',
                // 'exists:users,id',
                // Rule::exists('users', 'id')->where(function ($query) {
                //     $query->where('is_active', true);
                // })
            ],
            'status' => [
                'required',
                'string',
                'max:255'
            ],
            'visit_notes' => [
                'nullable',
                'string',
                'max:2000'
            ],
            'lead_id' => [
                'nullable',
                'exists:leads,id'
            ],
            'client_id' => [
                'nullable'
  
            ],
            'attachments' => [
                'nullable',
                'array',
                'max:5'
            ],
            'attachments.*' => [
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx',
                'max:5120'
            ]
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
            'visit_time.after_or_equal' => 'Visit time must be in the future.',
            'visit_time.before_or_equal' => 'Visit time cannot be more than 3 months in the future.',
            'visit_assignee.exists' => 'The selected assignee is invalid or not an active user.',
            'status.in' => 'Invalid status. Must be one of: scheduled, pending, confirmed.',
            'lead_id.required' => 'Either lead_id or client_id is required.',
            'client_id.required' => 'Either lead_id or client_id is required.',
            'visit_notes.max' => 'Notes cannot exceed 2000 characters.',
            'attachments.max' => 'Maximum 5 attachments allowed.',
            'attachments.*.max' => 'Each attachment must be less than 5MB.',
            'attachments.*.mimes' => 'Allowed file types: jpg, png, pdf, doc, docx.'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'created_by' => auth()->user()->uuid,
            'uuid' => Str::orderedUuid(),
            'visit_notes' => $this->visit_notes ? trim($this->visit_notes) : null,
            'lead_id' => $this->lead_id ?: null,
            'client_id' => $this->client_id ?: null
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'visit_time' => 'visit time',
            'visit_assignee' => 'assignee',
            'lead_id' => 'lead',
            'client_id' => 'client'
        ];
    }
}
