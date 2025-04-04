<?php

namespace Modules\Leads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:128',
            'contact_person' => 'required|string|max:64',
            'contact_person_role' => 'nullable|string|max:64',
            'email' => [
                'required',
                'email',
                'max:128',
                Rule::unique('leads')->whereNull('deleted_at')
            ],
            'phone' => 'required|string|max:16',
            'address' => 'nullable|string',
            'status' => [
                'required',
                'string',
                'max:32',
            ],
            'source' => 'nullable|string|max:64',
            // 'assigned_user' => 'required|uuid|exists:users,id',
            'note' => 'nullable|string',
            // 'visit_assignee' => 'nullable|uuid|exists:users,id',
            'visit_time' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already associated with another lead',
            'assigned_user.exists' => 'The selected user does not exist',
            'visit_assignee.exists' => 'The selected visit assignee does not exist',
            'status.in' => 'Invalid status value'
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('phone')) {
            $this->merge(['phone' => preg_replace('/[^0-9+]/', '', $this->phone)]);
        }
    }
}
