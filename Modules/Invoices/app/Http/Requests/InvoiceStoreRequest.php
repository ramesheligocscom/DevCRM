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
            'title' => 'nullable',
            'description' => 'nullable',
            'status' => 'required|string|max:32',
            'client_id' => 'nullable|uuid',
            'contract_id' => 'nullable|uuid',
            'quotation_id' => 'nullable|uuid',
        ];
    }
}
