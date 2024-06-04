<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoinRequest extends FormRequest
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
            'name' => 'required|string|max:50|unique:coins,name,' . $this->route('coin')->id,
            'symbol' => 'required|max:5|unique:coins,symbol,' . $this->route('coin')->id,
            'notes' => ['nullable', 'string', 'max:255'],
            'is_default' => ['boolean'],
        ];
    }
}
