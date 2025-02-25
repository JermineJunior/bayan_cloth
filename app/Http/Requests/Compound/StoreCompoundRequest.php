<?php

namespace App\Http\Requests\Compound;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompoundRequest extends FormRequest
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
            'constant_number' => 'required|numeric',
            'date' => 'required|date',
            'note' => 'required|string|max:225',
            'madin' => 'array',
            'madin.*' => 'required|numeric|exists:tree4s,id',
            'dain' => 'required|numeric|exists:tree4s,id',
            'price_dain' => 'required|numeric',
            'price_madin' => 'array',
            'price_madin.*' => 'required|numeric',
        ];
    }
}
