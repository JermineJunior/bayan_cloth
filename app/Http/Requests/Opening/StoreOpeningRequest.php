<?php

namespace App\Http\Requests\Opening;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpeningRequest extends FormRequest
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
            'account' => 'required|exists:tree4s,id',
            'accountType' => 'required|in:1,2',
            'price' => 'required|numeric',
            'date' => 'required|date',
            'constant_number' => 'required|numeric',
        ];
    }
}
