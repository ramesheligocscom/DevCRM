<?php

namespace Modules\Contracts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // return $this->user()->can('update', Contract::class);
        return true;

    }

    public function rules(): array
    {
        // Changed: Simplified status validation
        return [
            'items' => 'sometimes|array',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'sub_total' => 'sometimes|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'status' => 'sometimes|string|max:32',  // No enum validation
            'client_id' => 'nullable|uuid',        // No exists rule
            'quotation_id' => 'nullable|uuid',     // No exists rule
            'invoice_id' => 'nullable|uuid',       // No exists rule
        ];
    }

    public function messages(): array
    {
        // Changed: Removed reference-specific messages
        return [
            'end_date.after' => 'End date must be after start date',
        ];
    }
}
