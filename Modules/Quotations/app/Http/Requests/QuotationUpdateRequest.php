<?php

namespace Modules\Quotations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Quotations\Models\Quotation;

class QuotationUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $quotation = $this->route('quotation');

        return [
            // 'quotation_number' => 'sometimes|string|max:64|unique:quotations,quotation_number,'.$quotation->id,
            'valid_uptil' => 'sometimes|date',
            'quotation_type' => 'sometimes|string|max:32',
            'title' => 'sometimes|string|max:255',
            // 'sub_total' => 'sometimes|numeric|min:0',
            // 'discount' => 'nullable|numeric|min:0',
            // 'tax' => 'nullable|numeric|min:0',
            // 'total' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|max:32',
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
