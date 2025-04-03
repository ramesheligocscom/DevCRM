<?php

namespace Modules\Clients\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Or implement proper authorization logic
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_person_role' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clients')->whereNull('deleted_at')
            ],
            'phone' => 'required|string|max:20|phone:AUTO',
            'status' => [
                'required',
                'string',
                'max:50',
                Rule::in(['active', 'inactive', 'prospect', 'archived'])
            ],
            'assigned_user' => 'required|uuid|exists:users,id',
            'lead_id' => [
                'nullable',
                'uuid',
                Rule::exists('leads', 'id')->whereNull('deleted_at')
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already associated with another client',
            'assigned_user.exists' => 'The selected user does not exist',
            'lead_id.exists' => 'The referenced lead does not exist or was deleted'
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('phone')) {
            $this->merge(['phone' => preg_replace('/[^0-9+]/', '', $this->phone)]);
        }
    }
}
