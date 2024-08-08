<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreBillRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('bills')->where(function ($query) {
                    $query->where(DB::raw('COALESCE(user_id, 0)'), $this->input('user_id') ?? 0);
                }),
            ],
            'notes' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'array', 'min:1'],
            'amount.*' => ['required', 'numeric', 'min:0'],
            'user_id' => ['nullable', 'exists:users,id'],
            'is_crypto' => ['boolean'],
        ];
    }
}
