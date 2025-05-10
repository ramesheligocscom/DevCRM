<?php

namespace Modules\Quotations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuotationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valid_uptil' => 'required|date|after_or_equal:today',
            'quotation_type' => 'required|string|max:32',
            'title' => 'required|string|max:255',
            'status' => 'required|string|max:32',
            'items' => 'nullable|array',
            'custom_header_text' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'lead_id' => 'nullable|uuid',
            'client_id' => 'nullable|uuid',
            'contract_id' => 'nullable|uuid',
        ];
    }
}
