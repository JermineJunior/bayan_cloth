<?php

namespace App\Http\Requests\Revenue;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRevenueRequest extends FormRequest
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
            'dain' =>'required|exists:tree4s,id',
            'transaction_no' => ['nullable', 'string', 'max:100', Rule::requiredIf(fn () => (float) str_replace(',', '', $this->madin_name[1] ?? '') > 0)],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_no.required' => 'يرجى إدخال رقم عملية البنك.',
        ];
    }
}
