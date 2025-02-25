<?php

namespace App\Http\Requests\Daily;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyRequest extends FormRequest
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
            'madin' => 'required|exists:tree4s,id',
            'dain' => 'required|exists:tree4s,id',
            'date' => 'required|date',
            'price' => 'required|numeric'
        ];
    }
}
