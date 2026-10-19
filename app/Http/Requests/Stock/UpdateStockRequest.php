<?php

namespace App\Http\Requests\Stock;

use App\Models\Stock;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
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
        $stock = $this->route('stock');
        $stockId = $stock instanceof Stock ? $stock->id : $stock;

        return [
            'name' => ['required', Rule::unique('stocks', 'name')->ignore($stockId, 'id')],
            'branches' => 'required|array|min:1',
            'branches.*' => 'required|exists:branches,id',
        ];
    }
}
