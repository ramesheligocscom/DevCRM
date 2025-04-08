<?php

namespace Modules\Invoices\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'nullable|array',
            // 'sub_total' => 'required|numeric|min:0',
            // 'tax' => 'nullable|numeric|min:0',
            // 'discount' => 'nullable|numeric|min:0',
            // 'total' => 'required|numeric|min:0',
            'status' => 'required|string|max:32',
            'client_id' => 'nullable|uuid',
            'contract_id' => 'nullable|uuid',
        ];
    }
}
