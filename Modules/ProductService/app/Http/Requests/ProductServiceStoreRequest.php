<?php

namespace Modules\ProductService\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductServiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'attributes' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required',
            'price.numeric' => 'The price must be a number',
            'price.min' => 'The price cannot be negative',
            'attributes.array' => 'Attributes must be a Array string',
        ];
    }
}
