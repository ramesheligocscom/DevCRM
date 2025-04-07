<?php

namespace Modules\Clients\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Clients\app\Models\Client;

class ClientUpdateRequest extends FormRequest
{
    // public function authorize(): bool
    // {
    //     return $this->user()->can('update', Client::class);
    // }

    public function rules(): array
    {
        $clientId = $this->route('client'); // Gets the UUID from route parameter

        return [
            'name' => 'sometimes|string|max:255',
            'contact_person' => 'sometimes|string|max:255',
            'contact_person_role' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('clients')->ignore($clientId)->whereNull('deleted_at')
            ],
            'phone' => 'sometimes|string|max:20',
            'status' => [
                'sometimes',
                'string',
                'max:50',
                Rule::in(['active', 'inactive', 'prospect', 'archived'])
            ],
            'assigned_user' => [
                'sometimes',
                'uuid',
                // Rule::exists('users', 'id')
            ],
            'lead_id' => [
                'nullable',
                'uuid',
                Rule::exists('leads', 'id')->whereNull('deleted_at')
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already associated with another client',
            'assigned_user.exists' => 'The specified user does not exist',
            'lead_id.exists' => 'The referenced lead does not exist or was deleted',
            'status.in' => 'Invalid status value'
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
            'lead_id' => 'lead reference',
            'assigned_user' => 'assigned user'
        ];
    }
}
