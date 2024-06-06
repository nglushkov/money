<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlannedExpenseRequest extends FormRequest
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
            'amount' => ['required', 'numeric'],
            'day' => ['required', 'integer', 'min:1', 'max:31'],
            'month' => ['nullable', 'required_if:frequency,annually', 'integer', 'min:1', 'max:12'],
            'frequency' => ['required', 'in:monthly,annually'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'bill_id' => ['nullable', 'exists:bills,id'],
            'place_id' => ['required', 'exists:places,id'],
            'notes' => ['nullable', 'string'],
            'reminder_days' => ['nullable', 'integer', 'min:1', 'max:30'],
        ];
    }
}
