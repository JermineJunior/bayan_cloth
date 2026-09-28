<?php

namespace App\Http\Requests\PurchaseReturn;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'transaction_no' => ['nullable', 'string', 'max:100', Rule::requiredIf(fn () => (float) str_replace(',', '', $this->bank_amount_total ?? '') > 0)],
        ];
    }
}
