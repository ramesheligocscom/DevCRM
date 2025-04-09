<?php

namespace Modules\Leads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Leads\Models\Lead;

class LeadUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true ;
        // return $this->user()->can('update', Leads::class);
    }

    public function rules(): array
    {
        $leadId = $this->route('lead');

        return [
            'name' => 'sometimes|string|max:128',
            'contact_person' => 'sometimes|string|max:64',
            'contact_person_role' => 'nullable|string|max:64',
            'email' => [
                'sometimes',
                'email',
                'max:128',
                Rule::unique('leads')->ignore($leadId)->whereNull('deleted_at')
            ],
            'phone' => 'sometimes|string|max:16',
            'address' => 'nullable|string',
            'status' => [
                'sometimes',
                'string',
                'max:32',
                // Rule::in(['new', 'contacted', 'qualified', 'converted', 'lost'])
            ],
            'source' => 'nullable|string|max:64',
            'note' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already associated with another lead',
            'assigned_user.exists' => 'The specified user does not exist',
            'status.in' => 'Invalid status value',
            'client_id.exists' => 'The referenced client does not exist',
            'quotation_id.exists' => 'The referenced quotation does not exist',
            'contract_id.exists' => 'The referenced contract does not exist',
            'invoice_id.exists' => 'The referenced invoice does not exist'
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('phone')) {
            $this->merge(['phone' => preg_replace('/[^0-9+]/', '', $this->phone)]);
        }
    }

    public function attributes(): array
    {
        return [
            'assigned_user' => 'assigned user',
        ];
    }
}
