<?php

namespace App\Http\Requests;

use App\Models\Enum\OperationType;
use Illuminate\Foundation\Http\FormRequest;

class StoreOperationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bill_id' => 'required|exists:bills,id',
            'category_id' => 'required|exists:categories,id',
            'currency_id' => 'required|exists:currencies,id',
            'date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric',
            'type' => 'required|in:' . implode(',', OperationType::names()),
            'place_id' => 'required|exists:places,id',
            'notes' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:jpeg,png,pdf,zip|max:8192',
        ];
    }
}
