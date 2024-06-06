<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExchangeRequest extends FormRequest
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
            'from_currency_id' => 'required|exists:currencies,id|different:to_currency_id',
            'amount_from' => 'required|numeric',
            'to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
            'amount_to' => 'required|numeric',
            'bill_id' => 'required|exists:bills,id',
            'date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string',
            'place_id' => 'nullable|exists:exchange_places,id',
            'place_name' => 'nullable|string|max:255',
        ];
    }
}
