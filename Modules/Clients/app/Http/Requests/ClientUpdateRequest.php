<?php

namespace Modules\Clients\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'contact_person' => 'sometimes|string|max:255',
            'contact_person_role' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|string|max:20',
            'status' => 'sometimes|string|max:50',
            'assigned_user' => 'sometimes|string|max:255',
            'lead_id' => 'nullable|uuid|exists:leads,id',
            'is_deleted' => 'sometimes|boolean'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
