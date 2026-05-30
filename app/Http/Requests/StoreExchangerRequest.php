<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExchangerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'              => 'required|date',
            'place_id'          => 'nullable|exists:exchange_places,id',
            'from_bill_id'      => 'required|exists:bills,id',
            'from_currency_id'  => 'required|exists:currencies,id',
            'notes'             => 'nullable|string|max:500',
            'rows'              => 'required|array|min:1',
            'rows.*.from_amount'=> 'required|numeric|min:0.00000001',
            'rows.*.amount'     => 'required|numeric|min:0.00000001',
            'rows.*.currency_id'=> 'required|exists:currencies,id',
            'rows.*.bill_id'    => 'required|exists:bills,id',
        ];
    }
}
