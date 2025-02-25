<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
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
            'name' =>'required|string|max:255',
            'email' =>['nullable' ,'email' ,Rule::unique('suppliers','email')->ignore($this->supplier->id , 'id')],
            'phone' => ['required', Rule::unique('suppliers','phone')->ignore($this->supplier->id , 'id')],
            'address' => 'required|string|max:255'
        ];
    }
}
