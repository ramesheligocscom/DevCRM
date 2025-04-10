<?php

namespace Modules\Contracts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'=> 'nullable|string',
            'description'=> 'nullable|string',
            'items' => 'nullable|array',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:32',  
            'client_id' => 'nullable|uuid',    
            'quotation_id' => 'nullable|uuid', 
            'invoice_id' => 'nullable|uuid',   
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.after_or_equal' => 'Start date must be today or in the future',
            'end_date.after' => 'End date must be after start date',
        ];
    }
}
