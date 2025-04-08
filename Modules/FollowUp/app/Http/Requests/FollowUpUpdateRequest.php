<?php

namespace Modules\FollowUp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FollowUpUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */

    public function rules()
    {
        return [
            'call_status' => [
                'sometimes',
                Rule::in(['completed', 'pending', 'no_answer', 'busy', 'failed'])
            ],
            'lead_prospect' => 'sometimes|string|max:32',
            'call_summary' => 'nullable|string|max:2000',
            'lead_id' => [
                'nullable',
                'exists:leads,id',
                Rule::requiredIf(function () {
                    return empty($this->client_id);
                })
            ],
            'client_id' => [
                'nullable',
                'exists:clients,id',
                Rule::requiredIf(function () {
                    return empty($this->lead_id);
                })
            ],
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
