<?php

namespace App\Http\Requests\Transform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransformRequest extends FormRequest
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
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'stock_id_from' => ['required', Rule::exists('branch_stock', 'stock_id')->where('branch_id', session('branch_id'))],
            'stock_id_to' => ['required', Rule::exists('branch_stock', 'stock_id')->where('branch_id', session('branch_id'))],
            'qty' => 'required|array',
            'qty.*' => 'required|numeric',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|exists:units,id',
        ];
    }
}
