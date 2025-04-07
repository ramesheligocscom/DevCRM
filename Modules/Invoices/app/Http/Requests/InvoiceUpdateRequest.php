<?php

namespace Modules\Invoices\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'nullable|array',
            'sub_total' => 'sometimes|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|max:32',
            'client_id' => 'nullable|uuid',
            'contract_id' => 'nullable|uuid',
        ];
    }
}
