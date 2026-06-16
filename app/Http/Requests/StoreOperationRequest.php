<?php

namespace App\Http\Requests;

use App\Models\Enum\OperationType;
use Illuminate\Foundation\Http\FormRequest;

class StoreOperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $split = $this->boolean('split_mode');

        return [
            'bill_id'              => 'required|exists:bills,id',
            'currency_id'          => 'required|exists:currencies,id',
            'date'                 => 'required|date|before_or_equal:today',
            'amount'               => 'required|numeric',
            'type'                 => 'required|in:' . implode(',', OperationType::names()),
            'place_id'             => 'nullable|exists:places,id',
            'notes'                => 'nullable|string|max:255',
            'attachment'           => 'nullable|file|mimes:jpeg,png,pdf,zip|max:8192',
            'split_mode'           => 'nullable|boolean',

            // normal mode
            'category_id'          => $split ? 'nullable' : 'required|exists:categories,id',

            // split mode
            'splits'               => $split ? 'required|array|min:1' : 'nullable',
            'splits.*.category_id' => $split ? 'required|exists:categories,id' : 'nullable',
            'splits.*.amount'      => $split ? 'required|numeric|min:0.01' : 'nullable',
        ];
    }
}
