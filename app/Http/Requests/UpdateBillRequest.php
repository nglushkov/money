<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBillRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:bills,name,' . $this->route('bill')->id . ',id,user_id,' . auth()->id()],
            'notes' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'array'],
            'amount.*' => ['required', 'numeric'],
            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
