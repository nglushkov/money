<?php

namespace App\Http\Requests;

use App\Models\Enum\OperationType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOperationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
{
    return [
        'amount.required' => 'Amount is required',
    ];
}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'type' => 'required|in:' . implode(',', OperationType::names()),
            'bill_id' => 'required|exists:bills,id',
            'category_id' => 'required|exists:categories,id',
            'currency_id' => 'required|exists:currencies,id',
            'place_id' => 'required|exists:places,id',
            'notes' => 'nullable|string',
            'date' => 'required|date|before_or_equal:today',
            'attachment' => 'nullable|file|mimes:jpeg,png,pdf,zip|max:8192',
        ];
    }
}
