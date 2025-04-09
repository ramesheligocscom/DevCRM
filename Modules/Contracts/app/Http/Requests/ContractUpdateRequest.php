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
        return [
            'title'=> 'nullable|string',
            'description'=> 'nullable|string',
            'items' => 'sometimes|array',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'status' => 'sometimes|string|max:32',   
            'client_id' => 'nullable|uuid',        
            'quotation_id' => 'nullable|uuid',     
            'invoice_id' => 'nullable|uuid',       
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after' => 'End date must be after start date',
        ];
    }
}
